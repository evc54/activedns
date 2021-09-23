<?php

class ResourceRecordParser
{
  /**
   * @var string domain name
   */
  private $_domain;

  /**
   * @var string zone file contents
   */
  private $_content;

  /**
   * Constructor
   *
   * @param string $domain domain name
   * @param string $filename path to zone file
   */
  function __construct($domain,$filename)
  {
    if (file_exists($filename) && is_readable($filename)) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo,$filename);
      if ($mime == 'text/plain') {
        $this->_content = file_get_contents($filename);
      }
    }
    $this->_domain = $domain;
  }

  /**
   * Main parser
   */
  function parse()
  {
    $return = array();
    if (!empty($this->_content)) {
      $content = explode(PHP_EOL,$this->_content);
      $globalTtl = 3600;
      $directive = null;
      $nextLineType = null;
      $soa = '';
      $report = '';
      foreach ($content as $line) {
        $originLine = $line;
        // throw out a comment block
        if (stripos($line,';') !== false && stripos($line,'txt') === false) {
          $line = substr($line,0,stripos($line,';'));
        }
        // trim the rest of line
        $line = trim($line," \t\n\r");
        if (empty($line)) {
          // whole line is a comment, skipping
          $report[] = array(
            'origin'=>$originLine,
            'report'=>'skipped',
          );
          continue;
        }
        if ($line[0] == '$') {
          if (stripos($line,'TTL') !== false) {
            // this is global TTL set
            $globalTtl = $this->parseTtl(trim(substr($line,stripos($line,'TTL') + 1)));
          }
          else {
            // this is global directive, set current directive
            $directive = $line;
          }
          $report[] = array(
            'origin'=>$originLine,
            'report'=>'directive',
          );
        }
        elseif (stripos($line,'SOA') || $nextLineType == 'SOA') {
          // this is starting SOA block
          $soa .= $line . ' '; // this ending space for a delimiter of SOA parameters
          // check if SOA multiline
          $nextLineType = stripos($soa,')') === false ? 'SOA' : null;
          $report[] = array(
            'origin'=>$originLine,
            'report'=>'soa',
          );
        }
        else {
          // this goes resource record
          $parsed = $this->parseResourceRecord($directive,$globalTtl,$line,$originLine);
          if (isset($parsed['unsupported'])) {
            $report[] = array(
              'origin'=>$originLine,
              'report'=>'unsupported',
            );
          }
          elseif (isset($parsed['invalid'])) {
            $report[] = array(
              'origin'=>$originLine,
              'report'=>'invalid',
            );
          }
          else {
            $report[] = array(
              'origin'=>$originLine,
              'report'=>'resourcerecord',
            );
          }

          $rr[] = $parsed;
        }
      }

      if (preg_match_all('/^([\w-\.]+)\s*(\w+)\s*(\w+)\s*([\w-\.]+)\s*([\w-\.]+)\s*\s*\(\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*\)\s*$/',$soa,$matches)) {
        if (count($matches) == 11) {
          $soa = array(
            'domain'     => $matches[1][0],
            'class'      => $matches[2][0],
            'type'       => $matches[3][0],
            'primaryNS'  => $matches[4][0],
            'hostmaster' => $matches[5][0],
            'serial'     => $matches[6][0],
            'refresh'    => $matches[7][0],
            'retry'      => $matches[8][0],
            'expire'     => $matches[9][0],
            'minimum'    => $matches[10][0],
          );
        }
      }

      if (!is_array($soa)) {
        $soa = null;
      }
      
      $return['soa'] = $soa;
      $return['rr'] = $rr;
      $return['report'] = $report;
    }

    return $return;
  }

  /**
   * Parse TTL
   *
   * @param string $ttl
   * @return integer parsed TTL in secords
   */
  private function parseTtl($ttl)
  {
    if (intval($ttl) == $ttl) {
      // simple integer TTL
      return intval($ttl);
    }

    if (stripos($ttl,'s')) {
      // second
      return intval($ttl);
    }

    if (stripos($ttl,'m')) {
      // minute
      return intval($ttl) * 60;
    }

    if (stripos($ttl,'h')) {
      // hour
      return intval($ttl) * 3600;
    }

    if (stripos($ttl,'d')) {
      // day
      return intval($ttl) * 86400;
    }

    if (stripos($ttl,'w')) {
      // week
      return intval($ttl) * 604800;
    }

    // invalid data
    // fais se que dois adviegne que peut
    return intval($ttl);
  }

  private function parseResourceRecord($globalDirective, $globalTtl, $resourceRecordLine, $originLine)
  {
    $rr = array(
      'origin' => $originLine,
    );
    if (stripos($globalDirective,'ORIGIN') !== false) {
      // this is origin directive sensible for us
      $origin = trim(substr($globalDirective,stripos($globalDirective,'ORIGIN') + 7));
    }
    if (preg_match_all('/\s*(\S+)/',$resourceRecordLine,$line)) {
      $rr['parsed'] = $line[1];
      $line = $line[1];
      for ($i = 0; $i < count($line); $i++) {
        if (isset($rr['unsupported']) || isset($rr['done'])) {
          break;
        }
        if (empty($rr['type']) && preg_match('/^\d+[SMHW]*$/',$line[$i])) {
          // this is ttl
          $rr['ttl'] = $this->parseTtl($line[$i]);
        }
        elseif (strtoupper($line[$i]) == 'IN') {
          $rr['class'] = 'IN';
          if (empty($rr['host'])) {
            $rr['host'] = empty($origin) || $origin == '.' ? '@' : $origin;
          }
        }
        elseif ($this->checkResourceRecordType($line[$i])) {
          // this is resource record definition
          $rr['type'] = $line[$i];
        }
        elseif (!empty($rr['type'])) {
          switch ($rr['type']) {
            case 'A':
            case 'AAAA':
            case 'CNAME':
            case 'NS':
              $rr['rdata'] = $line[$i];
              $rr['done'] = true;
              break;
            case 'TXT':
              $rr['rdata'] = '';
              for ($j = $i; $j < count($line); $j++) {
                $rr['rdata'] .= $line[$j];
              }
              if ($rr['rdata'][0] == '"') {
                $rr['rdata'] = substr($rr['rdata'],1,-1);
              }
              $rr['done'] = true;
              break;
            case 'MX':
              if (empty($line[$i]) || empty($line[$i+1])) {
                $rr['unsupported'] = true;
                continue;
              }
              $rr['priority'] = $line[$i];
              $rr['rdata'] = $line[$i+1];
              $rr['done'] = true;
              break;
            case 'SRV':
              if ($rr['host'] == (empty($origin) || $origin == '.' ? $this->_domain . '.' : $origin)) {
                $rr['unsupported'] = true;
              }
              else {
                $rr['name'] = $rr['host'];
                $rr['priority'] = $line[$i];
                $srv = explode('.',$rr['host']);
                if (!isset($srv[1])) {
                  $rr['unsupported'] = true;
                  continue;
                }
                $rr['proto'] = $srv[1];
                if (!isset($line[$i+1]) || !isset($line[$i+2]) || !isset($line[$i+3])) {
                  $rr['unsupported'] = true;
                  continue;
                }
                $rr['weight'] = $line[$i+1];
                $rr['port'] = $line[$i+2];
                $rr['target'] = $line[$i+3];
                $rr['done'] = true;
              }
              break;
            default:
              $rr['unsupported'] = true;
          }
        }
        else {
          // assume that is hostname
          $rr['host'] = $line[$i];
          if ($rr['host'][0] != '@' && $rr['host'][0] != '*' && $rr['host'][strlen($rr['host'])-1] != '.') {
            $rr['host'] .= '.' . (empty($origin) ? $this->_domain . '.' : strtolower($origin));
          }
          if (empty($rr['host']) || $rr['host'] == '.') {
            $rr['host'] = '@';
          }
        }
      }
    }
    
    if (empty($rr['type'])) {
      $rr['invalid'] = true;
    }
    else {
      if (empty($rr['ttl'])) {
        $rr['ttl'] = $globalTtl;
      }
    }
    
    return $rr;
  }
  
  private function checkResourceRecordType($type)
  {
    $exists = false;

    if (in_array(strtoupper($type), array(
      'A',     'AAAA',     'AFSDB',      'APL',
      'CAA',   'CERT',     'CNAME',      'DHCID',
      'DLV',   'DNAME',    'DNSKEY',     'DS',
      'HIP',   'IPSECKEY', 'KEY',        'KX',
      'LOC',   'MX',       'NAPTR',      'NS',
      'NSEC',  'NSEC3',    'NSEC3PARAM', 'PTR',
      'RRSIG', 'RP',       'SIG',        'SOA',
      'SPF',   'SRV',      'SSHFP',      'TA',
      'TKEY',  'TLSA',     'TSIG',       'TXT',
    ))) {
      $exists = true;
    }

    return $exists;
  }
}
