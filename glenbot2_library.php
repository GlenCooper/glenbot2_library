<?php
# 
# glenbot2_library.php
# by Glen Cooper, glen@glencooper.com, bitcoin firstbits: 1GLENCo
#
# HISTORY:
# 201707310450: borrowed most of this from my ancient library file at http://glencooper.com/php/gcooper_library.txt
# 20181201T0129Z: going to try to use this on notice.glencooper.com, and figure out where to place it in foundation.glencooper.com
# 20181201T0143Z: use the "mylib" alias to vi this library file, and the "push" alias to scp it to foundation.glencooper.com
# 20181201T0149Z: this library will now be available at https://foundation.glencooper.com/glenbot2_library.phps
# 20181201T0201Z: changed permanent address to https://foundation.glencooper.com/glenbot2_library.txt so it can be more easily viewed in any browser
# 20181207T1738Z: this library file is now shared on github!  Repository URL = https://github.com/GlenCooper/glenbot2_library
# 20181215T0235Z: continued converting old pointcodes to timestamps.glencooper.com format... still not done yet
# 20181220T0548Z: made CLI functionality work better
#

if(!isset($colors))
{
  $colors = array();
}
$colors['green'] = '#11FF33';
$colors['red'] = 'red';
$colors['blue'] = 'blue';
$colors['yellow'] = 'yellow';
$colors['ltblue'] = '#99ccff';
$colors['ltgreen'] = '#ccffcc';
$colors['ltred'] = '#ffcccc';

if(!function_exists('debug_msg'))
{
  function debug_msg($msg,$debug_level=1,$include_timestamp=FALSE)
  {
    # This function will echo a debug message if debug_on is true.
    # By default, the debug_level is 1.  Increase this number (up to 10,000) to see more debugging info.
    # By default, timestamps in debug messages are disabled.  Pass a TRUE value to $include_timestamp to enable timestamps.
    # Timestamps can also be enabled by setting a global variable called $debug_on_include_timestamp.  When true, timestamps will be enabled.
    global $debug_on,$colors,$debug_on_include_timestamp;
    if(isset($debug_on_include_timestamp))
    {
      if($debug_on_include_timestamp)
      {
        $include_timestamp = TRUE;
      }
    }
    if($debug_on>=$debug_level)
    {
      if($include_timestamp)
      {
        $msg = date('YmdHis').','.$msg;
      }
      if(is_command_line_version())
      {
        echo "$msg\n";
      }
      else
      {
        echo "<center><table cellpadding=0 cellspacing=0 width=\"75%\"><tr><td>\n";
        $newmsg = "<font face=\"Courier New\" color=\"";
        if(isset($colors['debug']))
        {
          $newmsg.=$colors['debug']; 
        }
        else
        {
          $newmsg.= "#7fffd4";
        }
        $newmsg.= "\" size=\"-1\">".$msg."</font><br>\n";
        echo $newmsg;
        echo "</td></tr></table></center>\n";
        flush();
      }
    }
  }
}

function convert_all_arr_elements_to_html_special($arr)
{
  # This function will convert all $arr array elements to html special characters.
  # The returned function will be the same as the original except all data will
  # be converted to htmlspecialchars first.
  foreach($arr as $var => $val)
  {
    if(is_array($val))
    {
      debug_msg("200603010038: is_array(\"$var\") is TRUE");
      debug_msg("200603010041: NOTE: This part still needs to be written!!!");
      return $arr;
      #$new_arr["$var"] = array();
    }
    else
    {
      debug_msg("200603010039: is_array(\"$var\") is FALSE",1000);
      $new_arr["$var"] = htmlspecialchars($val);
    }
  }
  return $new_arr;
}

function define_colors()
{
  $colors = array();
  $colors['green'] = '#11FF33';
  $colors['red'] = 'red';
  $colors['blue'] = 'blue';
  $colors['yellow'] = 'yellow';
  $colors['ltblue'] = '#99ccff';
  $colors['ltgreen'] = '#ccffcc';
  $colors['ltred'] = '#ffcccc';
  $colors['white'] = '#ffffff';
  return $colors;
}

function is_valid_net_ipv4_parse_address($cidr)
{
  debug_msg("201602292237: function is_valid_net_ipv4_parse_address(\"$cidr\") START:");
  require_once('Net/IPv4.php');   # https://pear.php.net/manual/en/package.networking.net-ipv4.php
  $net = Net_IPv4::parseAddress($cidr);   # https://pear.php.net/manual/en/package.networking.net-ipv4.parseaddress.php
  if(PEAR::isError($net))    # https://pear.php.net/manual/en/core.pear.pear.iserror.php
  {
    debug_msg("201602292219: PEAR::isError(\$net) is TRUE!");
  }
  else
  {
    debug_msg("201602292222: converting \$net object to associative array");
    $stuff = (array) $net;
    debug_msg("201602292231: what does the \$stuff array look like?");
    debug_arr($stuff,'stuff');
    return $stuff;
  }
}

function is_valid_ip_v2($ip=FALSE,$which_value='',$silent_mode=FALSE)
{
  # This function was written to replace the original "is_valid_ip" function which used global variables.
  # This function was modified 201208031344 such that if an IP like "10.27.30.07" is passed to it, it
  # will return the syntactically correct "10.27.30.7" instead.
  $colors = define_colors();
  $green = $colors['green'];
  $red = $colors['red'];
  if(!$ip)
  {
    if(is_command_line_version())
    {
      echocolor("FATAL ERROR!\n",'light_red');
      echocolor("Script aborted!\n",'light_red');
      echocolor("POINTCODE: 201202211328\n",'light_red');
    }
    else
    {
      ech("<font color=\"$red\">FATAL ERROR!<br>Script aborted!</font><br>\n");
      ech("<font color=\"$red\">POINTCODE: 201202061609</font>\n");
    }
    exit;
  }
  $msg = "<li>Checking to see if";
  if($which_value)
  {
    $msg .= " $which_value";
  }
  else
  {
    $which_value = ' the IP Address';
  }
  $msg .= " ($ip) is a valid IP address... ";
  if(!($silent_mode))
  {
    ech($msg);
  }
  $ipaddress = 0;
  $ipaddress_portnumber = 0;
  $invalid_ip = 0;
  $valid_pattern = '/^(\d+)\.(\d+)\.(\d+)\.(\d+)(:(\d+))?$/';
  $gave_warning = 0;
  if(preg_match($valid_pattern,$ip,$ipmatches))
  {
    $octets[0] = $ipmatches[1];
    $octets[1] = $ipmatches[2];
    $octets[2] = $ipmatches[3];
    $octets[3] = $ipmatches[4] + 0;
    $ipaddress = $octets[0].'.'.$octets[1].'.'.$octets[2].'.'.$octets[3];
    if(isset($ipmatches[6]))
    {
      if($ipmatches[6])
      {
        $ipaddress_portnumber = $ipmatches[6];
        if(!($silent_mode))
        {
          ech("<font color=\"yellow\">WARNING:<br>\n");
          ech("There is also a port number ($ipaddress_portnumber) specified which will be ignored.&nbsp; $which_value will be interpreted as \"$ipaddress\".</font><br>\n");
        }
        $gave_warning = 1;
      }
    }
    if(count($octets)!=4)
    {
      debug_msg("200505052214: count(\$octets) != 4.  Returning false.");
      return false;
    }
    $octets_count = count($octets);
    debug_msg("200902180944: \$octets_count = \"$octets_count\"",1000);
    for($validloop=0;$validloop<$octets_count;$validloop++)
    {
      debug_msg("200902180943: \$validloop = \"$validloop\"",1000);
      if(!(($octets[$validloop]>=0)&&($octets[$validloop]<= 255)))
      {
        debug_msg("200505052215: \$octets[$validloop] is not between 0 and 255.  Returning false.");
        return false;
      }
    }
  }
  else
  {
    debug_msg("201602292235: no \$valid_pattern match for \"$ip\".");
    if(preg_match("/^\d+\.\d+\.\d+\.\d+\/\d+$/",$ip,$hit))
    {
      debug_msg("201606210907: matched IP and netmask format with CIDR notation.");
      if(is_valid_net_ipv4_parse_address($ip))
      {
        debug_msg("201606210911: returning TRUE");
        return TRUE;
      }
    }
    return false;
  }
  if(!($gave_warning))
  {
    if(!($silent_mode))
    {
      ech("<font color=\"$green\">It is.</font><br>\n");
    }
  }
  debug_msg("200505052217: returning TRUE.",500);
  return $ipaddress;
}

function is_valid_ip()
{
  global $ipaddress,$ipaddress_portnumber,$colors,$RealIPAddress_ep1,$debug_on;
  # function is_valid_ip($ip_addr,$which_value)
  # example: is_valid_ip("192.168.100.1",'EP2-RealIPAddress')
  debug_msg("200505052212: is_valid_ip function START",500);
  $num_args = func_num_args();
  debug_msg("200505052213: \$num_args = \"$num_args\"",1000);
  if($num_args>0)
  {
    $ip_addr = func_get_arg(0);
    debug_msg("200704201441: \$ip_addr = \"$ip_addr\"",100);
  }
  if($num_args>1)
  {
    $which_value = func_get_arg(1);
    debug_msg("200704201442: \$which_value = \"$which_value\"",100);
  }
  else
  {
    $which_value = '';
  }
  if($num_args>2)
  {
    $silent_mode = func_get_arg(2);
    debug_msg("200704191955: \$silent_mode = \"$silent_mode\"",100);
  }
  else
  {
    $silent_mode = FALSE;
  }
  $msg = "<li>Checking to see if";
  if($which_value != NULL)
  {
    $msg.= " $which_value";
  }
  if($ip_addr != NULL)
  {
    $msg.= " (\"$ip_addr\")";
  }
  $msg.= " is a valid IP address... ";
  if(!($silent_mode))
  {
    ech($msg);
  }
  $ipaddress = 0;
  $ipaddress_portnumber = 0;
  $invalid_ip = 0;
  $valid_pattern = '/^(\d+)\.(\d+)\.(\d+)\.(\d+)(:(\d+))?$/';
  $gave_warning = 0;
  if(preg_match($valid_pattern,$ip_addr,$ipmatches))
  {
    $octets[0] = $ipmatches[1];
    $octets[1] = $ipmatches[2];
    $octets[2] = $ipmatches[3];
    $octets[3] = $ipmatches[4];
    $ipaddress = $octets[0].'.'.$octets[1].'.'.$octets[2].'.'.$octets[3];
    if(isset($ipmatches[6]))
    {
      if($ipmatches[6])
      {
        $ipaddress_portnumber = $ipmatches[6];
        if(!($silent_mode))
        {
          echo "<font color=\"".$colors['yellow']."\">WARNING:<br>\n";
          echo "There is also a port number ($ipaddress_portnumber) specified which will be ignored.&nbsp; EP1-RealIPAddress will be interpreted as \"$ipaddress\".</font><br>\n";
        }
        $RealIPAddress_ep1 = $ipaddress;
        $gave_warning = 1;
      }
    }
    if(count($octets)!=4)
    {
     debug_msg("200505052214: count(\$octets) != 4.  Returning false.");
       return false;
    }
    $octets_count = count($octets);
    debug_msg("200902180944: \$octets_count = \"$octets_count\"",1000);
    for($validloop=0;$validloop<$octets_count;$validloop++)
    {
      debug_msg("200902180943: \$validloop = \"$validloop\"",1000);
      if(!(($octets[$validloop]>=0)&&($octets[$validloop]<= 255)))
      {
        debug_msg("200505052215: \$octets[$validloop] is not between 0 and 255.  Returning false.");
        return false;
      }
    }
  }
  else
  {
    debug_msg("200505052216: no pattern match.  Returning false.");
    return false;
  }
  if(!($gave_warning))
  {
    if(!($silent_mode))
    {
      ech("<font color=\"".$colors['green']."\">It is.</font><br>\n");
    }
  }
  debug_msg("200505052217: returning TRUE.",500);
  return true;
}

if(!(function_exists('debug_arr')))
{
  function debug_arr($arr,$arrname='',$debug_level=1,$html_special=FALSE)
  {
    # this function will echo the contents of an array if debug_on is true.
    global $debug_on,$colors;
    if(!($debug_on))
    {
      return;
    }
    if($debug_on>=$debug_level)
    {
      if(is_command_line_version())
      {
        debug_msg("200807221340: is_command_line_version() is TRUE",1000);
        if(isset($arrname))
        {
          debug_msg("200807221341: \$arrname is TRUE (\$arrname = \"$arrname\")",1000);
          if(is_object($arr))
          {
            echo "print_r(get_object_vars(\$$arrname)) START:\n";
          }
          else
          {
            echo "print_r(\$$arrname) START:\n";
          }
        }
        print_r($arr);
      }
      else
      {
        echo "<center><table cellpadding=0 cellspacing=0 width=\"75%\"><tr><td>\n";
        $msg = "<font face=\"Courier New\" color=\"";
        if(isset($colors['debug']))
        {
          $msg.=$colors['debug'];
        }
        else
        {
          $msg.="#7fffd4";
        }
        $msg.="\" size=\"3\"><pre>";
        echo $msg;
        if($arrname)
        {
          if(is_object($arr))
          {
            echo "print_r(get_object_vars(\$$arrname)) START:<br>\n";
          }
          else
          {
            echo "print_r(\$$arrname) START:<br>\n";
          }
        }
        if($html_special)
        {
          debug_msg("200603010030: next line will call convert_all_arr_elements_to_html_special(\$arr)...",1000);
          $html_special_arr = convert_all_arr_elements_to_html_special($arr);
          debug_msg("200603010031: done with convert_all_arr_elements_to_html_special(\$arr).",1000);
          print_r($html_special_arr);
        }
        else
        {
          if(is_object($arr))
          {
            print_r(get_object_vars($arr));
          }
          else
          {
            print_r($arr);
          }
        }
        if($arrname)
        {
          echo "print_r($$arrname) END.<br>\n";
        }
        echo "</pre></font>\n";
        echo "</td></tr></table></center>\n";
      }
    }
  }
}

function html_headers($title='',$include_javascript_header=FALSE,$bgcolor='',$text_color='',$link_color='',$vlink_color='',$alink_color='',$css_url=FALSE,$head_lines=FALSE,$bodytags=FALSE)
{
  # this function will echo the standard html headers.
  # arg1 = (optional) title
  # arg2 = (optional) if 1: include legacy select_and_auto_copy_javascript_header() which works for IE only.
  #                   if 2: include ZeroClipboard javascript header
  #                   if 3: include Any+Time javascript header
  # arg3 = (optional) bgcolor,     !-----------------------------!
  # arg4 = (optional) text color   !  If specifying a color in   !
  # arg5 = (optional) link color   !  hex code format, include   !
  # arg6 = (optional) vlink color  !  the # in the value passed  !
  # arg7 = (optional) alink color  !-----------------------------!
  # arg8 = (optional) URL of CSS stylesheet to use.  If used, all colors above
  #                   will be null'ed out.
  # arg9 = (optional) array of lines to include in between the <head> </head> tags.
  # arg10= (optional) bodytags : ANYTHING in this will be included within the <body> tag like this: <body ANYTHING>
  $num_args = func_num_args();
  echo "<html>\n";
  echo "<head>\n";
  if($title)
  {
    echo "<title>$title</title>\n";
  }
  if($include_javascript_header==1)
  {
    select_and_auto_copy_javascript_header();
  }
  elseif($include_javascript_header==2)
  {
    select_all_and_copy_javascript_header_zeroclipboard();
  }
  elseif($include_javascript_header==3)
  {
    any_time_javascript_header();
  }
  if($css_url)
  {
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_url\" />\n";
  }
  if($head_lines)
  {
    if(is_array($head_lines))
    {
      foreach($head_lines as $head_ord => $head_line)
      {
        echo "$head_line\n";
      }
    }
  }
  echo "</head>\n";
  echo "<body";
  if(!($css_url))
  {
    if($bgcolor)
    {
      echo " bgcolor=\"$bgcolor\"";
    }
    else
    {
      echo " bgcolor=\"#000000\"";
    }
    if($text_color)
    {
      echo " text=\"$text_color\"";
    }
    else
    {
      echo " text=\"#FFFFFF\"";
    }
    if($link_color)
    {
      echo " link=\"$link_color\"";
    }
    if($vlink_color)
    {
      echo " vlink=\"$vlink_color\"";
    }
    if($alink_color)
    {
      echo " alink=\"$alink_color\"";
    }
    if($bodytags)
    {
      echo " $bodytags";
    }
  }
  if($include_javascript_header==2)
  {
    echo " onLoad=\"init()\"";
  }
  echo ">\n";
}

function get_command_line_options()
{
  # This function returns an array of the command-line options which
  # were passed to the script at runtime.
  global $debug_on;
  if(!($_SERVER["argv"]))
  {
    return FALSE;
  }
  if(!(is_array($_SERVER["argv"])))
  {
    return FALSE;
  }
  foreach($_SERVER["argv"] as $ord => $argv)
  {
    if(preg_match("/-debug(=(\d+))?/",$argv,$hit))
    {
      if(isset($hit))
      {
        if(isset($hit[2]))
        {
          $debug_on = $hit[2];
        }
        else
        {
          $debug_on = 1;
        }
      }
      else
      {
        $debug_on = 1;
      }
      debug_msg("20060222T2250Z: Debug mode enabled.");
      debug_msg("20060222T2241Z: debug level is $debug_on.");
      if($debug_on==1)
      {
        debug_msg("20060222T2242Z: To see more debug output, increase the debug level:");
        debug_msg("20060222T2243Z: level 1 = least debug output, level 1000 = most debug output");
        debug_msg("20060222T2244Z: command-line param syntax to set debug level to 1000: -debug=1000");
      }
    }
    $options[] = $argv;
  }
  return($options);
}

if(!function_exists('define_hostname_short'))
{
  function define_hostname_short()
  {
    $stuff = preg_split("/\./",php_uname('n'));
    $hostname = $stuff[0];
    return $hostname;
  }
}

function script_aborted($pointcode)
{
  global $colors;
  debug_msg("200605280321: function script_aborted(\"$pointcode\") START");
  debug_msg("200605280322: next line will call tripwire(\"$pointcode\")...");
  tripwire("$pointcode");
  debug_msg("200605280323: done with tripwire(\"$pointcode\").");
  if(is_command_line_version())
  {
    echocolor("Script aborted.\n",'light_red');
    echocolor("Pointcode: $pointcode\n",'light_red');
  }
  else
  {
    echo "<br><img src=\"images/Homer_situation_critical.jpg\"><br>\n";
    echo "<font color=\"".$colors['red']."\">Script aborted.<br>\n";
    echo "Pointcode: $pointcode</font><br>\n";
    flush();
  }
  exit;
}

function define_bywho()
{
  # This function will attempt to figure out who is accessing the php script (web browser only).
  # If the function is able to determine who is accessing the php script, it will return that user's username.
  # If the function is not able to determine who is accesssing the php script, it will return FALSE.
  $lines = array();
  $bywhodata = array();
  $cmd = 'last -ai -n 5000 | grep logged';
  $output = `$cmd`;
  $rawlines = preg_split("/\n/",$output);
  foreach($rawlines as $ord => $line)
  {
    if($line)  # prevent empty lines from making it into the $lines array
    {
      $lines[] = $line;
    }
  }
  $linescount = count($lines);
  $pattern = '/(\w+)\s+pts\/\d+\s+(.+)\s+still logged in\s+(\d+\.\d+\.\d+\.\d+)/';
  foreach($lines as $ord => $line)
  {
    if(preg_match($pattern,$line,$hit))
    {
      $hit[2] = rtrim($hit[2]);
      $datepat = '/(\w+) (\w+)\s+(\d+)\s+(\d+):(\d+)/';
      if(preg_match($datepat,$hit[2],$matches))
      {
        $timestamp = "$matches[2] $matches[3] ".date("Y")." $matches[4]:$matches[5]";
      }
      $unixtimestamp = strtotime($timestamp);
      if(isset($bywhodata["$hit[3]"]))
      {
        if($unixtimestamp>$bywhodata["$hit[3]"]['when'])
        {
          $bywhodata["$hit[3]"]['who'] = $hit[1];
          $bywhodata["$hit[3]"]['when'] = $unixtimestamp;
        }
      }
      else
      {
        $bywhodata["$hit[3]"]['who'] = $hit[1];
        $bywhodata["$hit[3]"]['when'] = $unixtimestamp;
      }
    }
  }
  if(isset($_SERVER['REMOTE_ADDR']))
  {
    $remote_addr = $_SERVER['REMOTE_ADDR'];
  }
  else
  {
    return $_ENV['USER'];
  }
  if(isset($bywhodata["$remote_addr"]))
  {
    $bywho = $bywhodata["$remote_addr"]['who'];
    return $bywho;
  }
}

function any_time_javascript_header()
{
  # Use Any+Time DatePicker/TimePicker.  See http://www.ama3.com/anytime/
  # NOTE: This function ABSOLUTELY REQUIRES that the files exist in the specified path in order to work.
  # I also added sorttable.js to this as well (http://www.kryogenix.org/code/browser/sorttable/)
  #
  # 201707310509: erased the specific paths which had relations to TNS system names.
  # 201707310510: saving this only in case there's ever a need to reuse it someday.
  $anytime_css_path = 'http://foundation.glencooper.com/anytime.css';
  $jquery_path = 'http://foundation.glencooper.com/jquery.min.js';
  $anytime_js_path = 'http://foundation.glencooper.com/anytime.js';
  $sort_table_path = 'http://foundation.glencooper.com/sorttable.js';
  echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$anytime_css_path\" />\n";
  echo "<script src=\"$jquery_path\"></script>\n";
  echo "<script src=\"$anytime_js_path\"></script>\n";
  echo "<script src=\"$sort_table_path\"></script>\n";
}

function select_all_and_copy_javascript_header_zeroclipboard()
{
  # NOTE: This function ABSOLUTELY REQUIRES that the ZeroClipboard.js file exists at http://nocweb.corp.tnsi.com/imps/scripts/lvcbuilder/ZeroClipboard.js
  #                                 *** AND *** the ZeroClipboard.swf file exists at http://nocweb.corp.tnsi.com/imps/scripts/lvcbuilder/ZeroClipboard.swf
  # As of 201201171649 when this function was added, the ZeroClipboard website was online at http://code.google.com/p/zeroclipboard/
  # In case it disappears, the original tarball for v1.0.7 is saved to nocweb:/var/www/html/imps/scripts/lvcbuilder/zeroclipboard-1.0.7.tar.gz
  echo "<style type=\"text/css\">\n";
  echo "  body { font-family:arial,sans-serif; font-size:9pt; }\n";
  echo "  .my_clip_button { width:150px; text-align:center; border:1px solid black; background-color:#ccc; margin:10px; padding:10px; cursor:default; font-size:9pt; }\n";
  echo "  .my_clip_button.hover { background-color:#eee; }\n";
  echo "  .my_clip_button.active { background-color:#aaa; }\n";
  echo "</style>\n";
  echo "<script type=\"text/javascript\" src=\"http://nocweb.corp.tnsi.com/imps/scripts/lvcbuilder/ZeroClipboard.js\">\n";
  echo "ZeroClipboard.setMoviePath( 'http://nocweb.corp.tnsi.com/imps/scripts/lvcbuilder/ZeroClipboard.swf' );\n";
  echo "</script>\n";
  echo "<script language=\"JavaScript\">\n";
  echo "        var clip = null;\n";
  echo "\n";
  echo "        function $(id) { return document.getElementById(id); }\n";
  echo "\n";
  echo "        function init() {\n";
  echo "                clip = new ZeroClipboard.Client();\n";
  echo "                clip.setHandCursor( true );\n";
  echo "\n";
  echo "                clip.addEventListener('load', function (client) {\n";
  echo "                        debugstr(\"Flash movie loaded and ready.\");\n";
  echo "                });\n";
  echo "\n";
  echo "                clip.addEventListener('mouseOver', function (client) {\n";
  echo "                        // update the text on mouse over\n";
  echo "                        clip.setText( $('fe_text').value );\n";
  echo "                });\n";
  echo "\n";
  echo "                clip.addEventListener('complete', function (client, text) {\n";
  echo "                        debugstr(\"Copied text to clipboard: \" + text );\n";
  echo "                });\n";
  echo "\n";
  echo "                clip.glue( 'd_clip_button', 'd_clip_container' );\n";
  echo "        }\n";
  echo "\n";
  echo "        function debugstr(msg) {\n";
  echo "                var p = document.createElement('p');\n";
  echo "                p.innerHTML = msg;\n";
  echo "                $('d_debug').appendChild(p);\n";
  echo "        }\n";
  echo "</script>\n";
}

function list_of_files_in_directory()
{
  # This function returns an array of filenames in the specified $dir.
  # If the passed $dir does not end with a slash ('/'), one will be added.
  #
  # 1st argument: $dir
  # 2nd argument: if true, "." and ".." will be excluded from the returned list
  # 3rd argument: file(s) to exclude from the returned list.
  # 4th argument: only return a list of subdirectories of $dir
  # 5th argument: sort order :: 1 = regular sort (sort)
  #                          :: 2 = reverse sort (rsort)
  # 6th argument: if true, full pathnames will be returned.
  #               if false, only the filenames will be returned.
  # 7th argument: if true, 0-byte length files will be excluded from returned list
  # 8th argument: if true, only the number of files will be returned
  # 9th argument: array of patterns (regular expression format) to exclude.  Example: [0] = "/.+\.swp$/"
  # 10th argument: array of patterns of match on.  The array returned will be sorted by file modified date, most recently modified first.  Like "ls -lt pattern*"
  $num_args = func_num_args();
  $debug_msg = "200510202000: function list_of_files_in_directory(";
  if($num_args>0)
  {
    $dir = func_get_arg(0);
    $debug_msg.="\"$dir\"";
  }
  else
  {
    $dir = '';
  }
  if($num_args>1)
  {
    $no_dots = func_get_arg(1);
    $debug_msg.=",$no_dots";
  }
  else
  {
    $no_dots = FALSE;
  }
  if($num_args>2)
  {
    $exclude = func_get_arg(2);
    $debug_msg.=",$exclude";
  }
  else
  {
    $exclude = '';
  }
  if($num_args>3)
  {
    $arg3 = func_get_arg(3);
    $debug_msg.=",$arg3";
    if($arg3)
    {
      $subfolders_only = $arg3;
      unset($arg3);
    }
    else
    {
      $subfolders_only = 0;
    }
  }
  else
  {
    $subfolders_only = 0;
  }
  if($num_args>4)
  {
    $sort_method = func_get_arg(4);
    $debug_msg.=",$sort_method";
  }
  else
  {
    $sort_method = '';
  }
  if($num_args>5)
  {
    $return_full_pathnames = func_get_arg(5);
  }
  else
  {
    $return_full_pathnames = FALSE;
  }
  if($num_args>6)
  {
    $exclude_zero_length_files = func_get_arg(6);
  }
  else
  {
    $exclude_zero_length_files = 0;
  }
  if($num_args>7)
  {
    if(func_get_arg(7))
    {
      $only_count_of_files_in_dir = 1;
    }
    else
    {
      $only_count_of_files_in_dir = 0;
    }
  }
  else
  {
    $only_count_of_files_in_dir = 0;
  }
  $exclude_patterns = array();
  if($num_args>8)
  {
    if(func_get_arg(8))
    {
      if(is_array(func_get_arg(8)))
      {
        $exclude_patterns = func_get_arg(8);
        debug_msg("201003191203: what does the \$exclude_patterns array look like?");
        debug_arr($exclude_patterns,'exclude_patterns');
      }
    }
  }
  $match_on_patterns = FALSE;
  if($num_args>9)
  {
    if(func_get_arg(9))
    {
      if(is_array(func_get_arg(9)))
      {
        $match_on_patterns = func_get_arg(9);
        debug_msg("201503271650: what does the \$match_on_patterns array look like?");
        debug_arr($match_on_patterns,'match_on_patterns');
      }
    }
  }
  $debug_msg.=") START";
  debug_msg($debug_msg,100);
  debug_msg("200602081419: \$dir = \"$dir\"");
  
  # make sure the $dir ends with a '/'.  If not, add it.
  $lastchar = $dir[strlen($dir)-1];
  if(($lastchar==='/')||($lastchar==="\\"))
  {
    debug_msg("200602081418: \$dir ends with a slash.");
  }
  else
  {
    debug_msg("200705311905: \$dir did not end with a slash, so one has been added.");
    $dir = $dir.'/';
    debug_msg("200602081421: \$dir is now \"$dir\".");
  }

  $files = array();
  # the @ on the next line will suppress any warnings that may occur
  if ($handle = @ opendir("$dir"))
  {
    $dots1 = '.';
    $dots2 = '..';
    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle)))
    {
      if($no_dots)
      {
        if(($file==$dots1) || ($file==$dots2))
        {
          continue; # skip the rest of the current loop iteration
        }
      }
      if($exclude)
      {
        if(is_array($exclude))
        {
          if(in_array($file,$exclude))
          {
            continue; # skip the rest of the current loop iteration
          }
        }
        else
        {
          if($file==$exclude)
          {
            continue; # skip the rest of the current loop iteration
          }
        }
      }
      if($exclude_patterns)
      {
        foreach($exclude_patterns as $exclude_ord => $exclude_pattern)
        {
          if(preg_match("$exclude_pattern",$file))
          {
            continue(2);
          }
        }
      }
      if($subfolders_only)
      {
        if(!(is_dir($dir.$file)))
        {
          continue; # skip the rest of the current loop iteration
        }
      }
      if($exclude_zero_length_files)
      {
        if(@ filesize($dir.$file)==0)
        {
          continue;  # skip the rest of the current loop iteration
        }
      }
      if($match_on_patterns)
      {
        foreach($match_on_patterns as $pattern)
        {
          debug_msg("201503271729: \$pattern= $pattern");
          if(preg_match($pattern,$file))
          {
            debug_msg("201503271730: matched pattern to file \"$file\"");
          }
          else
          {
            debug_msg("201503271731: pattern did not match on file \"$file\"");
            continue(2);  # skip the rest of the current outer loop iteration
          }
        }
      }
      $files[] = $file;
    }
    closedir($handle);
    if($match_on_patterns)
    {
      debug_msg("201503271721: \$match_on_patterns is TRUE");
      $timestamped = array();
      foreach($files as $filename)
      {
        $full_filename = $dir.$filename;
        $timestamped[$filename] = filemtime($full_filename);
      }
      arsort($timestamped);  # sort array in reverse order and maintain index association
      debug_msg("201503271715: what does the \$timestamped array look like?");
      debug_arr($timestamped,'timestamped');
      $files = array();
      foreach($timestamped as $filename => $modified_timestamp)
      {
        $files[] = $filename;
        break;  # we only want the most recently modified file.
      }
    }
    if($return_full_pathnames)
    {
      $full_path_files = array();
      foreach($files as $fileord => $eachfile)
      {
        $full_path_files[] = $dir.$eachfile;
      }
      $files = $full_path_files;
    }
    if($sort_method)
    {
      if($sort_method==1)
      {
        sort($files);
      }
      elseif($sort_method==2)
      {
        rsort($files);
      }
    }
    if($only_count_of_files_in_dir)
    {
      $count_of_files = count($files);
      debug_msg("200704261910: \$count_of_files = \"$count_of_files\"");
      return $count_of_files;
    }
    else
    {
      debug_msg("200510202147: what does the \$files array look like?",500);
      debug_arr($files,'files',500);
      debug_msg("200510202204: returning \$files from list_of_files_in_directory().",1000);
      return $files;
    }
  }
}
function run_command($cmd,$split_into_lines=FALSE,$silent_mode=FALSE,$remove_blank_lines=FALSE)
{
  # this function runs a command ($cmd).
  global $colors;
  $num_args = func_num_args(0);
  if(!($silent_mode))
  {
    echo "<li>Running command: \"<font color=\"".$colors['ltblue']."\">$cmd</font>\"... ";
    flush();
  }
  $output = `$cmd`;
  if(!($silent_mode))
  {
    echo "<font color=\"".$colors['green']."\">Done</font>.<br>\n";
    flush();
  }
  if($split_into_lines)
  {
    $lines_array = preg_split("/\n/",$output);
    if($remove_blank_lines)
    {
      $lines_array = remove_blank_lines_from_array($lines_array);
    }
    return $lines_array;
  }
  return $output;
}

function take_out_the_trash($path,$days_to_keep=7)
{
  global $number_of_days_to_keep_log_files;
  if(isset($number_of_days_to_keep_log_files))
  {
    $days_to_keep = $number_of_days_to_keep_log_files;
  }
  $today = date("Ymd");
  debug_msg("201004131134: \$today = \"$today\"");
  $exclude_patterns = array();
  $exclude_patterns[] = "/^$today/";
  for($i=1;$i<$days_to_keep;$i++)
  {
    $minus_days = "-$i day";
    $numerical_date = date("Ymd",strtotime("$minus_days"));
    $exclude_patterns[] = "/^$numerical_date/";
  }
  debug_msg("201108221353: what does the \$exclude_patterns array look like?");
  debug_arr($exclude_patterns,'exclude_patterns');
  $list = list_of_files_in_directory($path,1,NULL,0,1,1,0,0,$exclude_patterns);
  debug_msg("201004131120: what does the \$list array look like?");
  debug_arr($list,'list');
  $all_old_logs_purged_successfully = TRUE;
  foreach($list as $filename)
  {
    # NOTE: Do not attempt to use echocolor within this function; it would cause a recursive call to this function
    #echocolor("Deleting old log file \"$filename\"... ",'light_blue');
    debug_msg("\n201004131229: next line is unlink(\"$filename\")...");
    if(unlink("$filename"))
    {
      #echocolor("Done.\n",'light_green');
    }
    else
    {
      echo "ERROR!\nA problem occurred when trying to delete an old log file!\n";
      $all_old_logs_purged_successfully = FALSE;
    }
  }
  debug_msg("201004131234: done with foreach loop");
  if($all_old_logs_purged_successfully)
  {
    debug_msg("201004131227: \$all_old_logs_purged_successfully is TRUE");
    return TRUE;
  }
  else
  {
    debug_msg("201004131228: \$all_old_logs_purged_successfully is FALSE");
  }
}

function remove_blank_lines_from_array($some_array)
{
  $new_array = array();
  foreach($some_array as $array_line)
  {
    if($array_line)
    {
      $new_array[] = $array_line;
    }
  }
  return $new_array;
}

function convert_seconds_to_human_time($seconds)
{
  # This function will convert a large number of seconds to a human readable
  # format like "18d 21h 40m 59s".

  # convert seconds to days
  $temp = $seconds/86400;
  $days=floor($temp);
  $temp=24*($temp-$days);
  $hours=floor($temp);
  $temp=60*($temp-$hours);
  $minutes=floor($temp);
  $temp=60*($temp-$minutes);
  $seconds=floor($temp);
  $output = '';
  if($days)
  {
    $output .= "{$days}d ";
  }
  if($hours)
  {
    $output .= "{$hours}h ";
  }
  if($minutes)
  {
    $output .= "{$minutes}m ";
  }
  if($seconds)
  {
    $output .= "{$seconds}s";
  }
  return $output;
}

function select_and_auto_copy_javascript_header()
{
  echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";                                      echo "<!-- Begin\n";
  echo "function copyit(theField) {\n";
  echo "var tempval=eval(\"document.\"+theField)\n";                              echo "tempval.focus()\n";
  echo "tempval.select()\n";
  echo "therange=tempval.createTextRange()\n";
  echo "therange.execCommand(\"Copy\")\n";                                        echo "}\n";
  echo "//  End -->\n";
  echo "</script>\n";
}

function select_all_and_copy()
{
  $num_args = func_num_args();
  if($num_args>0)
  {
    $txt = func_get_arg(0);
  }
  if($num_args>1)
  {
    $name = func_get_arg(1);
  }
  if($num_args>2)
  {
    $ech_verbose_level = func_get_arg(2);
  }
  else
  {
    $ech_verbose_level = '0';
  }
  if($num_args>3)
  {
    $text_left_of_select_button = func_get_arg(3);
  }
  else
  {
    $text_left_of_select_button = 0;
  }
  debug_msg("200708230058: \$text_left_of_select_button = \"$text_left_of_select_button\"",1000);
  if($num_args>4)
  {
    $word_wrap = func_get_arg(4);
    debug_msg("200710201500: \$word_wrap = \"$word_wrap\"");
  }
  else
  {
    $word_wrap = FALSE;
  }
  debug_msg("200510211412: function select_all_and_copy(\"$txt\",\"$name\") START",500);
  debug_msg("200510241526: need to get rid of .'s in the \$name (\"$name\").",1000);
  $formname = str_replace('.','_',$name);
  debug_msg("200510241527: \$formname = \"$formname\"",1000);
  ech("<form name=\"$formname\">\n",$ech_verbose_level);
  ech("<br><table border=0><tr>\n",$ech_verbose_level);
  ech("<td align=\"left\">");
  if($text_left_of_select_button)
  {
    ech("$text_left_of_select_button");
  }
  ech("</td>",$ech_verbose_level);
  ech("<td align=\"right\"><input onclick=\"copyit('$formname.textpart')\" type=\"button\" value=\"Select All and Copy\" name=\"cpy\"></td>\n",$ech_verbose_level,0,1);
  ech("</tr><tr>\n",$ech_verbose_level,0,1);
  $txt_count = count($txt);
  debug_msg("200511041529: \$txt_count = \"$txt_count\"",1000);
  if(is_array($txt))
  {
    $numrows = $txt_count + 1;
    $longest_line = 0;
    foreach($txt as $ord => $line)
    {
      if(strlen($line)>$longest_line)
      {
        $longest_line = strlen($line);
      }
    }
  }
  else
  {
    $numrows = 1;
    $longest_line = strlen($txt);
  }
  if($word_wrap)
  {
    $how_many_cols = 120;
    if(is_array($txt))
    {
      debug_msg("200710201501: is_array(\$txt) is TRUE");
      debug_msg("200710201507: what does the \$txt array look like?");
      debug_arr($txt,'txt');
      foreach($txt as $ord => $line)
      {
        $length_of_line = strlen($line);
        debug_msg("200710201503: \$length_of_line = \"$length_of_line\"",1000);
        if($length_of_line>$how_many_cols)
        {
          $numrows++;
          $need_to_round_this = (strlen($line))/($how_many_cols);
          $how_many_more_rows = round($need_to_round_this);
          $numrows = $numrows + $how_many_more_rows;
        }
      }
    }
    else
    {
      debug_msg("200710201501: is_array(\$txt) is TRUE");
      if(strlen($txt)>$how_many_cols)
      {
        $need_to_round_this = (strlen($line))/($how_many_cols);
        $how_many_more_rows = round($need_to_round_this);
        $numrows = $numrows + $how_many_more_rows;
      }
    }
  }
  else
  {
    $how_many_cols = $longest_line+2;
  }
  ech("<td colspan=2><textarea name=\"textpart\" rows=$numrows cols=$how_many_cols wrap=\"virtual\">\n",$ech_verbose_level,0,1);
  if(is_array($txt))
  {
    foreach($txt as $ord => $line)
    {
      ech("$line\n",$ech_verbose_level,0,1);
    }
  }
  else
  {
    ech($txt,$ech_verbose_level,0,1);
  }
  ech("</textarea></td></tr></table>\n",$ech_verbose_level);
  ech("</form>\n",$ech_verbose_level);
  debug_msg("200510211413: function select_all_and_copy(\"$txt\",\"$name\") END.",500);
}

function rebuild_options_without($omit)
{
  # this function manipulates the global variable $options string.  It returns a new $options string, sans and variables passed to it as arguments
  $num_args = func_num_args();
  for($i=0;$i<$num_args;$i++)
  {
    $passed_args[$i] = func_get_arg($i);
  }
  debug_msg("200512222123: function rebuild_options() START");
  if(!($passed_args))
  {
    return;
  }
  debug_msg("200512222115: next line will call explode_options()...");
  $options_arr = explode_options();
  debug_msg("200512222116: done with explode_options().");
  debug_msg("200512222120: what does the \$options_arr look like?");
  debug_arr($options_arr,'options_arr');
  if(is_array($options_arr))
  {
    foreach($options_arr as $var => $val)
    {
      if(!(in_array($var,$passed_args)))
      {
        $options_arr_new["$var"] = $val;
      }
    }
    $num_options = count($options_arr_new);
    $i = 0;
    if(!($options_arr_new))
    {
      return false;
    }
    $options_string_new = '';
    foreach($options_arr_new as $var => $val)
    {
      $options_string_new.="$var=$val";
      $i++;
      if($i<$num_options)
      {
        $options_string_new.="&";
      }
    }
    debug_msg("200512222135: \$options_string_new = \"$options_string_new\"");
    debug_msg("200512222138: returning \"$options_string_new\" from rebuild_options_without.");
    return $options_string_new;
  }
  else
  {
    return false;
  }
}

function explode_options()
{
  # this function returns an array containing all values in global variable $options
  debug_msg("200512222113: function explode_options() START");
  $options = get_options();
  $options_exploded = explode("&",$options);
  debug_msg("200512222114: what does \$options_exploded look like?");
  debug_arr($options_exploded,'options_exploded');
  if(is_array($options_exploded))
  {
    foreach($options_exploded as $ord => $option_pair)
    {
      list($var,$val) = split("=",$option_pair);
      $options_exploded_arr["$var"] = $val;
    }
    return $options_exploded_arr;
  }
  return false;
}

function get_options()
{
  # this function will gather the _GET values and reformat the in a way which
  # will make it easy to pass them back to $PHP_SELF?$options
  # Typical usage:
  # $options = get_options();
  #
  global $_GET;

  debug_msg("200502112149: function get_options START",1000);
  debug_arr($_GET,'_GET',1000);
  if(isset($options))
  {
    unset($options);
  }
  $options = '';
  foreach($_GET as $var => $val)
  {
    $options .= "$var=$val&";
  }
  $trimmed_options = rtrim($options,'&');
  $options = $trimmed_options;
  debug_msg("200502112156: \$options = \"$options\"",1000);
  debug_msg("200506231538: function get_options END",500);
  return $options;
}

function is_command_line_version()
{
  if(isset($_SERVER['HTTP_HOST'])) 
  {
    return false;
  }
  return true;
}

function newest_file()
{
  # this function checks the last modified timestamp of an array of files,
  # or a list of files, and returns whichever has the most recent timestamp.
  $args = func_get_args();
  foreach($args as $ord => $arg)
  {
    if(is_array($arg))
    {
      foreach($arg as $ord2 => $filename)
      {
        $files[] = $filename;
      }
    }
    else
    {
      $files[] = $arg;
    }
  }
  foreach($files as $ord => $filename)
  {
    if(file_exists($filename))
    {
      $timestamp = filemtime($filename);
      $table["$filename"] = $timestamp;
    }
  }
  if(is_array($table))
  {
    array_multisort($table,SORT_DESC);
  }
  else
  {
    echo "ERROR.  No valid files specified within newest() function.\n";
    echo "\n";
    echo "The newest() function compares the file timestamp of two files\n";
    echo "and returns the name of whichever file is the newest.\n";
    echo "\n";
    echo "Chances are you were trying to run a script on an invalid filename.\n";
    echo "POINTCODE: 200511180132\n";
    exit;
  }
  foreach($table as $filename => $timestamp)
  {
    # ugly way of accessing just the first in the list...
    return $filename;
  }
}

function is_permitted()  # NOTE: This function has been replaced with is_permitted_or_denied!
{
  global $ipaddress,$netmask,$inverse_netmask,$cidrbits,$network,$broadcast,$numhosts,$host_first,$host_last,$acloutput;
  $num_args = func_num_args();
  $source = func_get_arg(0);
  $dest = func_get_arg(1);
  $statement = func_get_arg(2);
  if($num_args>3)
  {
    $proto = func_get_arg(3);
  }
  else
  {
    $proto = 'ip';
  }

  debug_msg("200504062215: ------------------------------------------------------------------------------",100);
  debug_msg("200504062216: Checking to see if $source is permitted to pass $proto to $dest through \"$statement\"...",100);

  $host_host_pattern = "/^(access-list (\d+))? permit $proto host (\d+\.\d+\.\d+\.\d+) host (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $nw_nw_pattern = "/^(access-list (\d+))? permit $proto (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $host_nw_pattern = "/^(access-list (\d+))? permit $proto host (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $nw_host_pattern = "/^(access-list (\d+))? permit $proto (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) host (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $any_any_pattern = "/^(access-list (\d+))? permit $proto any any( log(-input)?)?$/";
  $any_nw_pattern = "/^(access-list (\d+))? permit $proto any (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  if(preg_match($host_host_pattern,$statement,$matches))
  {
    debug_msg("200504062217: access-list statement is host-to-host type.",100);
    if(($matches[3]==$source) && ($matches[4]==$dest))
    {
      debug_msg("200504062218: matched!  returning true.",100);
      return true;
    }
    else
    {
      debug_msg("200504062219: no match.");
    }
  }
  elseif(preg_match($any_nw_pattern,$statement,$matches))
  {
    debug_msg("200604120248: matched \$any_nw_pattern!",100);
    # the source portion will always be good on an any-to-network permit statement.
    $source_portion_is_good = 1;

    $dest_network = $matches[3];
    debug_msg("200604120249: \$dest_network = \"$dest_network\"",100);
    $dest_netmask = $matches[4];
    debug_msg("200604120250: \$dest_netmask = \"$dest_netmask\"",100);
    if(analyze_ip($dest_network,$dest_netmask))
    {
      debug_msg("200604120251: analyze_ip is TRUE!",100);
    }
    debug_msg("200604120252: \$source = \"$source\"",100);
    debug_msg("200604120253: \$host_first = \"$host_first\"",100);
    debug_msg("200604120254: \$host_last = \"$host_last\"",100);
    debug_msg("200604120255: \$network = \"$network\"",100);
    debug_msg("200604120256: \$broadcast = \"$broadcast\"",100);
    if((ip2long($source))>=(ip2long($network)) && (ip2long($source))<=(ip2long($broadcast)))
    {
      debug_msg("200604120257: looks good!",100);
      $dest_portion_is_good = 1;
    }
  }
  elseif(preg_match($nw_nw_pattern,$statement,$matches))
  {
    debug_msg("200712180400: access-list list statement is network to network type.",100);
    $source_network = $matches[3];
    debug_msg("200602221857: \$source_network = \"$source_network\"",100);
    $source_netmask = $matches[4];
    debug_msg("200602221858: \$source_netmask = \"$source_netmask\"",100);
    $dest_network = $matches[5];
    debug_msg("200602221859: \$dest_network = \"$dest_network\"",100);
    $dest_netmask = $matches[6];
    debug_msg("200602221900: \$dest_netmask = \"$dest_netmask\"",100);
    debug_msg("200603272000: next line will call if(analyze_ip(\"$source_network\",\"$source_netmask\")...",100);
    if(analyze_ip($source_network,$source_netmask,1))
    {
      debug_msg("200504062205: analyze_ip is TRUE",100);
    }
    else
    {
      debug_msg("200603272001: analyze_ip is FALSE",100);
    }
    debug_msg("200603272004: \$source = \"$source\"",750);
    debug_msg("200603272006: \$host_first = \"$host_first\"",750);
    debug_msg("200603272007: \$host_last = \"$host_last\"",750);
    debug_msg("200603272008: \$network = \"$network\"",750);
    debug_msg("200603272009: \$broadcast = \"$broadcast\"",750);
    if($source_ip)
    {
      debug_msg("200712181809: \$source_ip is TRUE.  This means it's a CIDR notation source.");
      $source_portion_is_good = 0;
    }
    else
    {
      if((ip2long($source))>=(ip2long($network)) && (ip2long($source))<=(ip2long($broadcast)))
      {
        debug_msg("200504062206: The source part is looking good.",100);
        $source_portion_is_good = 1;
      }
      else
      {
        debug_msg("200603272005: The source part is NOT looking good.",100);
      }
    }
    debug_msg("200603272010: next line will call if(analyze_ip(\"$dest_network\",\"$dest_netmask\")...",1000);
    if(analyze_ip($dest_network,$dest_netmask))
    {
      debug_msg("200504062207: analyze_ip returned TRUE");
    }
    else
    {
      debug_msg("200504062208: analyze_ip returned FALSE");
    }
    debug_msg("200602221908: \$dest = \"$dest\"");
    debug_msg("200602221909: \$host_first = \"$host_first\"");
    debug_msg("200602221910: \$host_last = \"$host_last\"");
    debug_msg("200602221912: \$network = \"$network\"");
    debug_msg("200602221913: \$broadcast = \"$broadcast\"");
    debug_msg("200602221901: ip2long(\$dest) = \"".ip2long($dest)."\"");
    debug_msg("200602221902: ip2long(\$host_first) = \"".ip2long($host_first)."\"");
    debug_msg("200602221902: ip2long(\$host_last) = \"".ip2long($host_last)."\"");

    # Fixed a bug in the following line 2/22/06:
    #if ((ip2long($dest))>=(ip2long($host_first)) && (ip2long($dest))<=(ip2long($host_last)))
    #
    # The above line was causing a problem when trying to use the acl script like this:
    # acl ftr03 192.168.1.42 63.160.212.0
    #
    # ... when the following line was in ftr03's config;
    # access-list 102 permit ip 192.168.1.0 0.0.0.255 63.160.212.0 0.0.3.255
    #
    if ((ip2long($dest))>=(ip2long($network)) && (ip2long($dest))<=(ip2long($broadcast)))
    {
      debug_msg("200504062209: the dest part is looking good",100);
      $dest_portion_is_good = 1;
    }
    else
    {
      debug_msg("200504062210: Not permitted.  Returning false.",100);
      return false;
    }
    if (($source_portion_is_good) && ($dest_portion_is_good))
    {
      debug_msg("200504062211: returning true from is_permitted...",100);
      return true;
    }
    return false;
  }
  elseif(preg_match($host_nw_pattern,$statement,$matches))
  {
    debug_msg("200504062221: access-list statement matches host to network pattern.",100);
    if($matches[3] != "$source")
    {
      debug_msg("200504062222: source host is different.",100);
      return false;
    }
    else
    {
      $source_portion_is_good = 1;
      debug_msg("200504062223: host portion matches.",100);
    }
    $dest_network = $matches[4];
    $dest_mask = $matches[5];
    debug_msg("200504062224: \$dest_network = \"$dest_network\"",100);
    debug_msg("200504062225: \$dest_mask = \"$dest_mask\"",100);
    if (analyze_ip($dest_network,$dest_mask))
    {
      debug_msg("200504062226: analyze_ip returned TRUE.");
    }
    else
    {
      debug_msg("200504062227: analyze_ip returned FALSE");
    }
    debug_msg("200504062228: \$ipaddress = \"$ipaddress\"");
    debug_msg("200504062228: \$netmask = \"$netmask\"");
    debug_msg("200504062228: \$inverse_netmask = \"$inverse_netmask\"");
    debug_msg("200504062228: \$cidrbits = \"$cidrbits\"");
    debug_msg("200504062228: \$network = \"$network\"");
    debug_msg("200504062228: \$broadcast = \"$broadcast\"");
    debug_msg("200504062228: \$numhosts = \"$numhosts\"");
    debug_msg("200504062228: \$host_first = \"$host_first\"");
    debug_msg("200504062228: \$host_last = \"$host_last\"");

    #if ((ip2long($dest))>=(ip2long($host_first)) && (ip2long($dest))<=(ip2long($host_last)))
    if ((ip2long($dest))>=(ip2long($network)) && (ip2long($dest))<=(ip2long($broadcast)))
    {
      debug_msg("200504062229: setting \$dest_portion_is_good = 1.");
      $dest_portion_is_good = 1;
    }
    else
    {
      debug_msg("200504062230: not permitted.  returning false...");
      return false;
    }
  }
  elseif(preg_match($nw_host_pattern,$statement,$matches))
  {
    debug_msg("200504062231: access-list statement matches network to host pattern.",100);
    $source_network = $matches[3];
    debug_msg("200706261241: \$source_network = \"$source_network\"",100);
    $source_netmask = $matches[4];
    debug_msg("200706261242: \$source_netmask = \"$source_netmask\"",100);
    $dest_host = $matches[5];
    debug_msg("200706261243: \$dest_host = \"$dest_host\"",100);
    if($dest_host == "$dest")
    {
      debug_msg("200504062232: same destination host.  setting \$dest_portion_is_good = 1...",100);
      $dest_portion_is_good = 1;
    }
    else
    {
      debug_msg("200504062233: different destination host.  returning false...",100);
      return false;
    }
    if(analyze_ip($source_network,$source_netmask))
    {
      debug_msg("200706250000: analyze_ip returned TRUE");
    }
    else
    {
      debug_msg("200504062235: analyze_ip returned FALSE");
      return false;
    }
    debug_msg("200706261253: \$source = \"$source\"",100);
    debug_msg("200706261254: \$host_first = \"$host_first\"",100);
    debug_msg("200706261255: \$host_last = \"$host_last\"",100);
    debug_msg("200706261256: \$network = \"$network\"",100);
    debug_msg("200706261257: \$broadcast = \"$broadcast\"",100);
    if((ip2long($source))>=(ip2long($network)) && (ip2long($source))<=(ip2long($broadcast)))
    {
      debug_msg("200706261244: setting \$source_portion_is_good = 1.");
      $source_portion_is_good = 1;
    }
    else
    {
      debug_msg("200706261245: source portion is not good.");
    }
  }
  elseif(preg_match($any_any_pattern,$statement,$matches))
  {
    debug_msg("200506090050: access-list statement matches any any pattern",100);
    return true;
  }
  if (($source_portion_is_good) && ($dest_portion_is_good))
  {
    debug_msg("200504062236: \$source_portion_is_good AND \$dest_portion_is_good");
    debug_msg("200504062237: returning true...");
    return true;
  }
  debug_msg("200504062238: returning FALSE...");
  return false;
}

function bitmask_from_netmask($netmask)
{
  $convert = array();
  # FORMAT = $convert[NETMASK] = $bitmask
  $convert['128.0.0.0'] = 1;
  $convert['192.0.0.0'] = 2;
  $convert['224.0.0.0'] = 3;
  $convert['240.0.0.0'] = 4;
  $convert['248.0.0.0'] = 5;
  $convert['252.0.0.0'] = 6;
  $convert['254.0.0.0'] = 7;
  $convert['255.0.0.0'] = 8;
  $convert['255.128.0.0'] = 9;
  $convert['255.192.0.0'] = 10;
  $convert['255.224.0.0'] = 11;
  $convert['255.240.0.0'] = 12;
  $convert['255.248.0.0'] = 13;
  $convert['255.252.0.0'] = 14;
  $convert['255.254.0.0'] = 15;
  $convert['255.255.0.0'] = 16;
  $convert['255.255.128.0'] = 17;
  $convert['255.255.192.0'] = 18;
  $convert['255.255.224.0'] = 19;
  $convert['255.255.240.0'] = 20;
  $convert['255.255.248.0'] = 21;
  $convert['255.255.252.0'] = 22;
  $convert['255.255.254.0'] = 23;
  $convert['255.255.255.0'] = 24;
  $convert['255.255.255.128'] = 25;
  $convert['255.255.255.192'] = 26;
  $convert['255.255.255.224'] = 27;
  $convert['255.255.255.240'] = 28;
  $convert['255.255.255.248'] = 29;
  $convert['255.255.255.252'] = 30;
  $convert['255.255.255.254'] = 31;
  $convert['255.255.255.255'] = 32;
  if(in_array($netmask,array_keys($convert)))
  {
    $cidr = $convert[$netmask];
    debug_msg("201403071623: returning \"$cidr\" from bitmask_from_netmask(\"$netmask\")...");
    return $cidr;
  }
}

function inverse_netmask_to_netmask($inverse_netmask)
{
  $convert = array();
  # FORMAT = $convert[INVERSE MASK] = NETMASK;
  $convert['127.255.255.255'] = '128.0.0.0';
  $convert['63.255.255.255'] = '192.0.0.0';
  $convert['31.255.255.255'] = '224.0.0.0';
  $convert['15.255.255.255'] = '240.0.0.0';
  $convert['7.255.255.255'] = '248.0.0.0';
  $convert['3.255.255.255'] = '252.0.0.0';
  $convert['1.255.255.255'] = '254.0.0.0';
  $convert['0.255.255.255'] = '255.0.0.0';
  $convert['0.127.255.255'] = '255.128.0.0';
  $convert['0.63.255.255'] = '255.192.0.0';
  $convert['0.31.255.255'] = '255.224.0.0';
  $convert['0.15.255.255'] = '255.240.0.0';
  $convert['0.7.255.255'] = '255.248.0.0';
  $convert['0.3.255.255'] = '255.252.0.0';
  $convert['0.1.255.255'] = '255.254.0.0';
  $convert['0.0.255.255'] = '255.255.0.0';
  $convert['0.0.127.255'] = '255.255.128.0';
  $convert['0.0.63.255'] = '255.255.192.0';
  $convert['0.0.31.255'] = '255.255.224.0';
  $convert['0.0.15.255'] = '255.255.240.0';
  $convert['0.0.7.255'] = '255.255.248.0';
  $convert['0.0.3.255'] = '255.255.252.0';
  $convert['0.0.1.255'] = '255.255.254.0';
  $convert['0.0.0.255'] = '255.255.255.0';
  $convert['0.0.0.127'] = '255.255.255.128';
  $convert['0.0.0.63'] = '255.255.255.192';
  $convert['0.0.0.31'] = '255.255.255.224';
  $convert['0.0.0.15'] = '255.255.255.240';
  $convert['0.0.0.7'] = '255.255.255.248';
  $convert['0.0.0.3'] = '255.255.255.252';
  $convert['0.0.0.1'] = '255.255.255.254';
  $convert['0.0.0.0'] = '255.255.255.255';
  if(in_array($inverse_netmask,array_keys($convert)))
  {
    $netmask = $convert[$inverse_netmask];
    debug_msg("201403071609: returning \"$netmask\" from inverse_netmask_to_netmask(\"$inverse_netmask\")...");
    return $netmask;
  }
}

function is_cidr_route($x)
{
  # This function analyzes $x to determine whether it's an ip/netmask in cidr notation.
  # If $x matches the pattern, an array will be returned with the ip and cidr netmask parts.
  $pattern = "/(\d+\.\d+\.\d+\.\d+)\/(\d+)/";
  if(preg_match($pattern,$x,$hit))
  {
    $return_stuff['ip'] = $hit[1];
    $return_stuff['nm'] = $hit[2];
    return $return_stuff;
  }
}

function is_permitted_or_denied($source,$dest=FALSE,$statement=FALSE,$proto=FALSE,$permit_or_deny=FALSE,$standard_acl=FALSE)
{
  global $ipaddress,$netmask,$inverse_netmask,$cidrbits,$network,$broadcast,$numhosts,$host_first,$host_last,$acloutput;
  debug_msg("200712180720: function is_permitted_or_denied(\"$source\",\"$dest\",\"$statement\",\"$proto\",\"$permit_or_deny\",\"$standard_acl\") START:",100);
  # Initialize variables (required for PHP5)
  $source_portion_is_good = 0;
  $dest_portion_is_good = 0;
  $proto = '';
  $source_ip = '';
  $dest_ip = '';

  $num_args = func_num_args();
  if($parts = is_cidr_route($source))
  {
    $source_ip = $parts['ip'];
    $source_nm = $parts['nm'];
  }
  if($dest)
  {
    if($parts = is_cidr_route($dest))
    {
      $dest_ip = $parts['ip'];
      $dest_nm = $parts['nm'];
    }
  }
  if(!($proto))
  {
    $proto = 'ip';
  }
  if($permit_or_deny)
  {
    $permit_or_deny = 'deny';
  }
  else
  {
    $permit_or_deny = 'permit';
  }
  debug_msg("200712180715: \$permit_or_deny = \"$permit_or_deny\"",100);
  debug_msg("200802080226: \$standard_acl = \"$standard_acl\"",500);
  debug_msg("200712180714: ------------------------------------------------------------------------------",100);
  if($permit_or_deny==='permit')
  {
    debug_msg("200712180713: Checking to see if $source is permitted to pass $proto traffic to $dest through \"$statement\"...",100);
  }
  else
  {
    debug_msg("200712180712: Checking to see if $source is explicitly denied from passing $proto traffic to $dest through \"$statement\"...",100);
  }

  $host_host_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+host (\d+\.\d+\.\d+\.\d+) host (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $nw_nw_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+(\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $host_nw_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+host (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $nw_host_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+(\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) host (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $any_any_pattern = "/^(access-list (\d+))? $permit_or_deny(\s+)$proto any any( log(-input)?)?$/";
  debug_msg("201503091118: \$any_any_pattern = \"$any_any_pattern\"",500);
  $any_nw_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+any (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $any_host_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+any host (\d+\.\d+\.\d+\.\d+)( log(-input)?)?$/";
  $nw_any_pattern = "/^(access-list (\d+))? $permit_or_deny\s+$proto\s+(\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) any( log(-input)?)?$/";
  $standard_num_pattern = "/^(access-list (\d+))? $permit_or_deny\s+(\d+\.\d+\.\d+\.\d+)$/";
  $standard_num_pattern_with_mask = "/^(access-list (\d+))? $permit_or_deny\s+(\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+)$/";
  if(preg_match($host_host_pattern,$statement,$matches))
  {
    debug_msg("200712180711: access-list statement is host-to-host type.",100);
    if(($matches[3]==$source) && ($matches[4]==$dest))
    {
      debug_msg("200712180710: matched!  returning true.",100);
      return true;
    }
    else
    {
      debug_msg("200712180709: no match.",100);
    }
  }
  elseif(preg_match($standard_num_pattern,$statement,$matches))
  {
    debug_msg("200806260015: access-list statement matched \$standard_num_pattern.",100);
    debug_msg("200806260018: the dest portion will always be good on a standard numbered access-list.",100);
    debug_msg("200806260019: setting \$dest_portion_is_good = 1...",100);
    $dest_portion_is_good = 1;
    debug_msg("200806260020: \$source = \"$source\"",100);
    $network_portion = $matches[3];
    $netmask_portion = '32';
    if($analyzed = analyze_ip($network_portion,$netmask_portion,1))
    {
      debug_msg("200806260151: analyze_ip(\"$network_portion\",\"$netmask_portion\" is TRUE",100);
    }
    debug_msg("200806260150: what does the \$analyzed array look like?");
    debug_arr($analyzed,'analyzed');
    debug_msg("200806260149: \$source = \"$source\"");
    if((ip2long($source))>=(ip2long($analyzed['network'])) && (ip2long($source))<=(ip2long($analyzed['broadcast'])))
    {
      debug_msg("200806260148: setting \$source_portion_is_good = 1!");
      $source_portion_is_good = 1;
    }
  }
  elseif(preg_match($standard_num_pattern_with_mask,$statement,$matches))
  {
    debug_msg("200806260016: access-list statement matched \$standard_num_pattern_with_mask.");
    debug_msg("200806260018: the dest portion will always be good on a standard numbered access-list.",100);
    debug_msg("200806260019: setting \$dest_portion_is_good = 1...",100);
    $dest_portion_is_good = 1;
    $network_portion = $matches[3];
    $netmask_portion = $matches[4];
    if($analyzed = analyze_ip($network_portion,$netmask_portion,1))
    {
      debug_msg("200806260025: analyze_ip(\"$network_portion\",\"$netmask_portion\" is TRUE",100);
    }
    debug_msg("200806260027: what does the \$analyzed array look like?");
    debug_arr($analyzed,'analyzed');
    debug_msg("200806260030: \$source = \"$source\"");
    if((ip2long($source))>=(ip2long($analyzed['network'])) && (ip2long($source))<=(ip2long($analyzed['broadcast'])))
    {
      debug_msg("200806260032: setting \$source_portion_is_good = 1!");
      $source_portion_is_good = 1;
    }
  }
  elseif(preg_match($any_host_pattern,$statement,$matches))
  {
    debug_msg("200908201528: matched \$any_host_pattern!",100);
    # the source portion will always be good on an any-to-host permit statement.
    $source_portion_is_good = 1;

    $dest_host = $matches[3];
    debug_msg("200908201533: \$dest_host = \"$dest_host\"",100);
    if($dest_ip)
    {
      debug_msg("200908201535: \$dest_ip is TRUE.  This means the dest is CIDR notation.");
      if($dest_nm==='32')
      {
        $dest = $dest_ip;
      }
    }
    if($dest===$dest_host)
    {
      debug_msg("200908201534: looks good!",100);
      $dest_portion_is_good = 1;
    }
  }
  elseif(preg_match($nw_any_pattern,$statement,$matches))
  {
    debug_msg("201109141537: matched \$nw_any_pattern!",100);
    # the dest portion will always be good on a network-to-any permit statement.
    $dest_portion_is_good = 1;

    $source_network = $matches[3];
    debug_msg("200712180707: \$source_network = \"$source_network\"",100);
    $source_netmask = $matches[4];
    debug_msg("200712180706: \$source_netmask = \"$source_netmask\"",100);

    require_once('Net/IPv4.php');
    // create IPv4 object
    $ip_calc = new Net_IPv4();

    // set variables
    $ip_calc->ip = $source;
    $normal_netmask = inverse_netmask_to_netmask($source_netmask);
    $cidr = bitmask_from_netmask($normal_netmask);
    debug_msg("201403071627: \$cidr = \"$cidr\"");
    $net1 = "$source_network/$cidr";
    debug_msg("201403071628: \$net1 = \"$net1\"");
    debug_msg("201403071630: \$source = \"$source\"");
    $in_range = Net_IPv4::ipInNetwork($source,$net1);
    debug_msg("201403071631: \$in_range = \"$in_range\"");
    if($in_range)
    {
      debug_msg("201403051639: source ip is within the range",100);
      $source_portion_is_good = 1;
    }
    else
    {
      debug_msg("201403051629: source portion is not within the range!",100);
    }
  }
  elseif(preg_match($any_nw_pattern,$statement,$matches))
  {
    debug_msg("200712180708: matched \$any_nw_pattern!",100);
    # the source portion will always be good on an any-to-network permit statement.
    $source_portion_is_good = 1;

    $dest_network = $matches[3];
    debug_msg("200712180707: \$dest_network = \"$dest_network\"",100);
    $dest_netmask = $matches[4];
    debug_msg("200712180706: \$dest_netmask = \"$dest_netmask\"",100);
    require_once('Net/IPv4.php');
    // create IPv4 object
    $ip_calc = new Net_IPv4();

    // set variables
    $ip_calc->ip = $dest;
    $normal_netmask = inverse_netmask_to_netmask($dest_netmask);
    $cidr = bitmask_from_netmask($normal_netmask);
    debug_msg("201407082202: \$cidr = \"$cidr\"");
    $net1 = "$dest_network/$cidr";
    debug_msg("201407082203: \$net1 = \"$net1\"");
    debug_msg("201407082204: \$dest = \"$dest\"");
    $in_range = Net_IPv4::ipInNetwork($dest,$net1);
    debug_msg("201407082205: \$in_range = \"$in_range\"");

    #if($analyze_ip_arr = analyze_ip($dest_network,$dest_netmask,1))
    #{
    #  debug_msg("200712180706: analyze_ip_arr is TRUE!",100);
    #  debug_msg("201104121655: what does the \$analyze_ip_arr array look like?");
    #  debug_arr($analyze_ip_arr,'analyze_ip_arr');
    #}
    #if(((ip2long($dest))>=($analyze_ip_arr['nw'])) && ((ip2long($dest))<=($analyze_ip_arr['bc'])))    # This method was flawed as of 201407082215
    # using the old method, acl -debug 100 seo01 224.0.26.56 224.0.26.56 was failing
    if($in_range)
    {
      debug_msg("201407082213: dest looks good!",100);
      $dest_portion_is_good = 1;
    }
  }
  elseif(preg_match($nw_nw_pattern,$statement,$matches))
  {
    debug_msg("200712181805: access-list list statement is network to network type.",100);
    require_once('Net/IPv4.php');
    // create IPv4 object
    $ip_calc = new Net_IPv4();

    $source_network = $matches[3];
    debug_msg("200712181806: \$source_network = \"$source_network\"",100);
    $source_netmask = $matches[4];
    debug_msg("200712181807: \$source_netmask = \"$source_netmask\"",100);
    $source_normal_netmask = inverse_netmask_to_netmask($source_netmask);
    $cidr = bitmask_from_netmask($source_normal_netmask);
    debug_msg("201403100940: \$cidr = \"$cidr\"");
    $net1 = "$source_network/$cidr";
    debug_msg("201403100941: \$net1 = \"$net1\"");

    $dest_network = $matches[5];
    debug_msg("200712181808: \$dest_network = \"$dest_network\"",100);
    $dest_netmask = $matches[6];
    debug_msg("200712181809: \$dest_netmask = \"$dest_netmask\"",100);
    // create IPv4 object
    $ip_calc = new Net_IPv4();
    $dest_normal_netmask = inverse_netmask_to_netmask($dest_netmask);
    debug_msg("201403110951: \$dest_normal_netmask = \"$dest_normal_netmask\"");
    $cidr = bitmask_from_netmask($dest_normal_netmask);
    $net2 = "$dest_network/$cidr";
    debug_msg("201403100947: \$net2 = \"$net2\"");
    
    debug_msg("200712181810: next line will call if(analyze_ip(\"$source_network\",\"$source_netmask\")...",100);
    if($analyze_ip_arr = analyze_ip($source_network,$source_netmask,1))
    {
      debug_msg("200712181811: analyze_ip is TRUE",100);
      $host_first = $analyze_ip_arr['host_first'];
      $host_last = $analyze_ip_arr['host_last'];
      $network = $analyze_ip_arr['network'];
      $broadcast = $analyze_ip_arr['broadcast'];
    }
    else
    {
      debug_msg("200712181812: analyze_ip is FALSE");
    }
    debug_msg("200712181813: \$source = \"$source\"");
    debug_msg("200712181814: \$host_first = \"$host_first\"");
    debug_msg("200712181815: \$host_last = \"$host_last\"");
    debug_msg("200712181816: \$network = \"$network\"");
    debug_msg("200712181817: \$broadcast = \"$broadcast\"");
    if($source_ip)
    {
      debug_msg("200712180930: \$source_ip is TRUE.  This means the source is CIDR notation.");
      if($analyzed_source_ip_arr = analyze_ip($source_ip,$source_nm,1))
      {
        debug_msg("200712181837: what does the \$analyzed_source_ip_arr array look like?");
        debug_arr($analyzed_source_ip_arr,'analyzed_source_ip_arr');
        if((ip2long($analyzed_source_ip_arr['network']))>=(ip2long($network)) && (ip2long($analyzed_source_ip_arr['broadcast']))<=(ip2long($broadcast)))
        {
          debug_msg("200712181852: The source part is looking good.",100);
          $source_portion_is_good = 1;
        }
        else
        {
          debug_msg("200712181853: The source part is NOT looking good.",100);
          $source_portion_is_good = 0;
        }
      }
      else
      {
        this_should_never_happen("200712181854");
      }
    }
    else
    {
      debug_msg("200801182343: something might not be right in the following section...");
      if((ip2long($source))>=(ip2long($network)) && (ip2long($source))<=(ip2long($broadcast)))
      {
        debug_msg("200712181818: The source part is looking good.",100);
        $source_portion_is_good = 1;
      }
      else
      {
        debug_msg("200712181819: The source part is NOT looking good.",100);
      }
    }
    debug_msg("200801182349: need to take into consideration what would happen if the dest is cidr notation here");
    if($dest_ip)
    {
      debug_msg("200801182350: \$dest_ip is TRUE.  This means the dest is CIDR notation.");
      if($analyzed_dest_ip_arr = analyze_ip($dest_ip,$dest_nm,1))
      {
        debug_msg("200801182351: what does the \$analyzed_dest_ip_arr array looke like?");
        debug_arr($analyzed_dest_ip_arr,'analyzed_dest_ip_arr');
        $analyzed_dest_in_statement = analyze_ip($dest_network,$dest_netmask,1);
        if((ip2long($analyzed_dest_ip_arr['network']))>=(ip2long($analyzed_dest_in_statement['network'])) && (ip2long($analyzed_dest_ip_arr['broadcast']))<=(ip2long($analyzed_dest_in_statement['broadcast'])))
        {
          debug_msg("200801182352: The dest part is looking good.",100);
          $dest_portion_is_good = 1;
        }
      }
    }
    else
    {
      debug_msg("200712181820: next line will call if(analyze_ip(\"$dest_network\",\"$dest_netmask\")...",1000);
      if(analyze_ip($dest_network,$dest_netmask))
      {
        debug_msg("200712181821: analyze_ip returned TRUE");
      }
      else
      {
        debug_msg("200712181822: analyze_ip returned FALSE");
      }
      debug_msg("200712181823: \$dest = \"$dest\"");
      debug_msg("200712181824: \$host_first = \"$host_first\"");
      debug_msg("200712181825: \$host_last = \"$host_last\"");
      debug_msg("200712181826: \$network = \"$network\"");
      debug_msg("200712181827: \$broadcast = \"$broadcast\"");
      debug_msg("200712181829: ip2long(\$dest) = \"".ip2long($dest)."\"");
      debug_msg("200712181830: ip2long(\$host_first) = \"".ip2long($host_first)."\"");
      debug_msg("200712181831: ip2long(\$host_last) = \"".ip2long($host_last)."\"");

      if ((ip2long($dest))>=(ip2long($network)) && (ip2long($dest))<=(ip2long($broadcast)))
      {
        debug_msg("200712180500: the dest part is looking good",100);
        $dest_portion_is_good = 1;
      }
      else
      {
        if($permit_or_deny === 'permit')
        {
          debug_msg("200712180501: Not permitted.  Returning false.",100);
        }
        else
        {
          debug_msg("200712180502: Not denied.  Returning false.",100);
          return false;
        }
      }
    }
    if (($source_portion_is_good) && ($dest_portion_is_good))
    {
      debug_msg("200712180503: returning true from is_permitted_or_denied...",100);
      return true;
    }
    debug_msg("200712180504: returning false from is_permitted_or_denied...",100);
    return false;
  }
  elseif(preg_match($host_nw_pattern,$statement,$matches))
  {
    debug_msg("200712180505: access-list statement matches host to network pattern.",100);
    if($matches[3] != "$source")
    {
      debug_msg("200712180506: source host is different.",100);
      return false;
    }
    else
    {
      $source_portion_is_good = 1;
      debug_msg("200712180507: host portion matches.",100);
    }
    $dest_network = $matches[4];
    $dest_mask = $matches[5];
    debug_msg("200712180508: \$dest_network = \"$dest_network\"",100);
    debug_msg("200712180509: \$dest_mask = \"$dest_mask\"",100);
    if($analyzed_dest_arr = analyze_ip($dest_network,$dest_mask,1))
    {
      debug_msg("200712180510: analyze_ip returned TRUE.");
      $ipaddress = $analyzed_dest_arr['ipaddress'];
      $netmask = $analyzed_dest_arr['netmask'];
      $inverse_netmask = $analyzed_dest_arr['inverse_netmask'];
      $cidrbits = $analyzed_dest_arr['cidrbits'];
      $network = $analyzed_dest_arr['network'];
      $broadcast = $analyzed_dest_arr['broadcast'];
      $numhosts = $analyzed_dest_arr['numhosts'];
      $host_first = $analyzed_dest_arr['host_first'];
      $host_last = $analyzed_dest_arr['host_last'];
    }
    else
    {
      debug_msg("200712180511: analyze_ip returned FALSE");
    }
    debug_msg("200712180512: \$ipaddress = \"$ipaddress\"");
    debug_msg("200712180513: \$netmask = \"$netmask\"");
    debug_msg("200712180514: \$inverse_netmask = \"$inverse_netmask\"");
    debug_msg("200712180515: \$cidrbits = \"$cidrbits\"");
    debug_msg("200712180516: \$network = \"$network\"");
    debug_msg("200712180517: \$broadcast = \"$broadcast\"");
    debug_msg("200712180518: \$numhosts = \"$numhosts\"");
    debug_msg("200712180519: \$host_first = \"$host_first\"");
    debug_msg("200712180520: \$host_last = \"$host_last\"");

    if($dest_ip)
    {
      debug_msg("200712181938: \$dest_ip is TRUE.  This means the dest is CIDR notation");
      if($analyzed_dest_ip_arr = analyze_ip($dest_ip,$dest_nm,1))
      {
        if((ip2long($analyzed_dest_ip_arr['network']))>=(ip2long($network)) && (ip2long($analyzed_dest_ip_arr['broadcast']))<=(ip2long($broadcast)))
        {
          debug_msg("200712181945: setting \$dest_portion_is_good = 1.");
          $dest_portion_is_good = 1;
        }
        else
        {
          debug_msg("200712181946: setting \$dest_portion_is_good = 0.");
          $dest_portion_is_good = 0;
        }
      }
      else
      {
        this_should_never_happen("200712181942");
      }
    }
    else
    {
      debug_msg("200712181946: \$dest_ip is FALSE.  This means the dest is not CIDR notation");
      if((ip2long($dest))>=(ip2long($network)) && (ip2long($dest))<=(ip2long($broadcast)))
      {
        debug_msg("200712180521: setting \$dest_portion_is_good = 1.");
        $dest_portion_is_good = 1;
      }
      else
      {
        debug_msg("200712180522: returning false from is_permitted_or_denied...");
        return false;
      }
    }
  }
  elseif(preg_match($nw_host_pattern,$statement,$matches))
  {
    debug_msg("200712180523: access-list statement matches network to host pattern.",100);
    $source_network = $matches[3];
    debug_msg("200802201844: \$source_network = \"$source_network\"",1000);
    $source_netmask = $matches[4];
    debug_msg("200802201845: \$source_netmask = \"$source_netmask\"",1000);
    $dest_host = $matches[5];
    if($dest_host == "$dest")
    {
      debug_msg("200712180524: same destination host.  setting \$dest_portion_is_good = 1...",100);
      $dest_portion_is_good = 1;
    }
    else
    {
      debug_msg("200712180525: different destination host.  returning false from is_permitted_or_denied...",100);
      return false;
    }
    if(analyze_ip($source_network,$source_netmask))
    {
      debug_msg("200712180526: analyze_ip returned TRUE");
    }
    else
    {
      debug_msg("200712180527: analyze_ip returned FALSE");
      return false;
    }
    if($source_ip)
    {
      debug_msg("200712272310: \$source_ip is TRUE.  This means the source is CIDR notation");
      if($analyzed_source_ip_arr = analyze_ip($source_ip,$source_nm,1))
      {
        debug_msg("200712272311: what does the \$analyzed_source_ip_arr array look like?");
        debug_arr($analyzed_source_ip_arr,'analyzed_source_ip_arr');
        debug_msg("200712272317: \$network = \"$network\"");
        debug_msg("200712272318: \$broadcast = \"$broadcast\"");
        if((ip2long($analyzed_source_ip_arr['network']))>=(ip2long($network)) && (ip2long($analyzed_source_ip_arr['broadcast']))<=(ip2long($broadcast)))
        {
          debug_msg("200712272314: setting \$source_portion_is_good = 1....");
          $source_portion_is_good = 1;
        }
      }
      else
      {
        this_should_never_happen("200712272310");
      }
    }
    else
    {
      debug_msg("200712272309: \$source_ip is FALSE.  This means the source is not CIDR notation");
      if((ip2long($source))>=(ip2long($network)) && (ip2long($source))<=(ip2long($broadcast)))
      {
        $source_portion_is_good = 1;
      }
    }
  }
  elseif(preg_match($any_any_pattern,$statement,$matches))
  {
    debug_msg("200712180528: access-list statement matches any any pattern",100);
    return true;
  }
  else
  {
    debug_msg("201503091117: matched none of the patterns",100);
  }
  if (($source_portion_is_good) && ($dest_portion_is_good))
  {
    debug_msg("200712180529: \$source_portion_is_good AND \$dest_portion_is_good");
    debug_msg("200712180530: returning true...");
    return true;
  }
  debug_msg("200712180531: returning FALSE...",1000);
  return false;
}

function ip2bin($ip)
{
  # Converts an IP Address to binary representation.
  # by Glen Cooper, www.GlenCooper.com
  if(preg_match("/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/",$ip,$hit))
  {
    $bin_octet1_short = decbin($hit[1]);
    $bin_octet1_long = substr("00000000",0,8 - strlen($bin_octet1_short)).$bin_octet1_short;
    $bin_octet2_short = decbin($hit[2]);
    $bin_octet2_long = substr("00000000",0,8 - strlen($bin_octet2_short)).$bin_octet2_short;
    $bin_octet3_short = decbin($hit[3]);
    $bin_octet3_long = substr("00000000",0,8 - strlen($bin_octet3_short)).$bin_octet3_short;
    $bin_octet4_short = decbin($hit[4]);
    $bin_octet4_long = substr("00000000",0,8 - strlen($bin_octet4_short)).$bin_octet4_short;
    $final_answer = $bin_octet1_long.'.'.$bin_octet2_long.'.'.$bin_octet3_long.'.'.$bin_octet4_long;
    #debug_msg("201007131552: \$final_answer = \"$final_answer\"");
    return $final_answer;
  }
}

function analyze_ip()
{
  # This function analyzes an IP Address and Netmask.
  # $arg1 = $ip_addr
  # $arg2 = $mask
  # $arg3 = if true, function will return an array of results
  #         if false, global variables will be set

  # $mask can be:
  # CIDR bit length ('24' for example),
  # the actual netmask ('255.255.255.0'),
  # or the inverse netmask ('0.0.0.255').
  #
  debug_msg("200603271959: function analyze_ip START",1000);
  $return_array_of_results = NULL;
  $ip_addr = func_get_arg(0);
  $myopts = "\"$ip_addr\"";
  debug_msg("200604062248: \$ip_addr = \"$ip_addr\"",600);
  $mask = func_get_arg(1);
  $myopts.= ",\"$mask\"";
  debug_msg("200604062249: \$mask = \"$mask\"",600);
  $num_args = func_num_args();
  debug_msg("200512212055: \$num_args = \"$num_args\"",1000);
  if($num_args>2)
  {
    debug_msg("200512212053: \$num_args is greater than 2.",1000);
    $return_array_of_results = func_get_arg(2);
    $myopts.=",\"$return_array_of_results\"";
  }

  if($return_array_of_results)
  {
    $use_globals = 0;
  }
  else
  {
    $use_globals = 1;
  }

  if($use_globals)
  {
    debug_msg("200604062242: \$use_globals is TRUE",1000);
    global $analyze_ip,$ipaddress,$netmask,$inverse_netmask,$cidrbits,$network,$broadcast,$numhosts,$host_first,$host_last;
  }
  else
  {
    debug_msg("200604062242: \$use_globals is FALSE",1000);
  }

  $numhosts = 0;
  if($mask=='255.255.255.255' || $mask=='32' || $mask=='0.0.0.0')
  {
    $netmask = '255.255.255.255';
    $inverse_netmask = '0.0.0.0';
    $cidrbits = 32;
    $valid_mask = 1;
    $numhosts = 0;
  }
  elseif($mask=='255.255.255.254' || $mask=='31' || $mask=='0.0.0.1')
  {
    $netmask = '255.255.255.254';
    $inverse_netmask = '0.0.0.1';
    $cidrbits = 31;
    $valid_mask = 1;
    $numhosts = 0;
  }
  elseif($mask=='255.255.255.252' || $mask=='30' || $mask=='0.0.0.3')
  {
    $netmask = '255.255.255.252';
    $inverse_netmask = '0.0.0.3';
    $cidrbits = 30;
    $valid_mask = 1;
    $numhosts = 2;
  }
  elseif($mask=='255.255.255.248' || $mask=='29' || $mask=='0.0.0.7')
  {
    $netmask = '255.255.255.248';
    $inverse_netmask = '0.0.0.7';
    $cidrbits = 29;
    $valid_mask = 1;
    $numhosts = 6;
  }
  elseif($mask=='255.255.255.240' || $mask=='28' || $mask=='0.0.0.15')
  {
    $netmask = '255.255.255.240';
    $inverse_netmask = '0.0.0.15';
    $cidrbits = 28;
    $valid_mask = 1;
    $numhosts = 14;
  }
  elseif($mask=='255.255.255.224' || $mask=='27' || $mask=='0.0.0.31')
  {
    $netmask = '255.255.255.224';
    $inverse_netmask = '0.0.0.31';
    $cidrbits = 27;
    $valid_mask = 1;
    $numhosts = 30;
  }
  elseif($mask=='255.255.255.192' || $mask=='26' || $mask=='0.0.0.63')
  {
    $netmask = '255.255.255.192';
    $inverse_netmask = '0.0.0.63';
    $cidrbits = 26;
    $valid_mask = 1;
    $numhosts = 62;
  }
  elseif($mask=='255.255.255.128' || $mask=='25' || $mask=='0.0.0.127')
  {
    $netmask = '255.255.255.128';
    $inverse_netmask = '0.0.0.127';
    $cidrbits = 25;
    $valid_mask = 1;
    $numhosts = 126;
  }
  elseif($mask=='255.255.255.0' || $mask=='24' || $mask=='0.0.0.255')
  {
    $netmask = '255.255.255.0';
    $inverse_netmask = '0.0.0.255';
    $cidrbits = 24;
    $valid_mask = 1;
    $numhosts = 254;
  }
  elseif($mask=='255.255.254.0' || $mask=='23' || $mask=='0.0.1.255')
  {
    $netmask = '255.255.254.0';
    $inverse_netmask = '0.0.1.255';
    $cidrbits = 23;
    $valid_mask = 1;
    $numhosts = 510;
  }
  elseif($mask=='255.255.252.0' || $mask=='22' || $mask=='0.0.3.255')
  {
    $netmask = '255.255.252.0';
    $inverse_netmask = '0.0.3.255';
    $cidrbits = 22;
    $valid_mask = 1;
    $numhosts = 1022;
  }
  elseif($mask=='255.255.248.0' || $mask=='21' || $mask=='0.0.7.255')
  {
    $netmask = '255.255.248.0';
    $inverse_netmask = '0.0.7.255';
    $cidrbits = 21;
    $valid_mask = 1;
    $numhosts = 2046;
  }
  elseif($mask=='255.255.240.0' || $mask=='20' || $mask=='0.0.15.255')
  {
    $netmask = '255.255.240.0';
    $inverse_netmask = '0.0.15.255';
    $cidrbits = 20;
    $valid_mask = 1;
    $numhosts = 4094;
  }
  elseif($mask=='255.255.224.0' || $mask=='19' || $mask=='0.0.31.255')
  {
    $netmask = '255.255.224.0';
    $inverse_netmask = '0.0.31.255';
    $cidrbits = 19;
    $valid_mask = 1;
    $numhosts = 8190;
  }
  elseif($mask=='255.255.192.0' || $mask=='18' || $mask=='0.0.63.255')
  {
    $netmask = '255.255.192.0';
    $inverse_netmask = '0.0.63.255';
    $cidrbits = 18;
    $valid_mask = 1;
    $numhosts = 16382;
  }
  elseif($mask=='255.255.128.0' || $mask=='17' || $mask=='0.0.127.255')
  {
    $netmask = '255.255.128.0';
    $inverse_netmask = '0.0.127.255';
    $cidrbits = 17;
    $valid_mask = 1;
    $numhosts = 32766;
  }
  elseif($mask=='255.255.0.0' || $mask=='16' || $mask=='0.0.255.255')
  {
    $netmask = '255.255.0.0';
    $inverse_netmask = '0.0.255.255';
    $cidrbits = 16;
    $valid_mask = 1;
    $numhosts = 65534;
  }
  elseif($mask=='255.254.0.0' || $mask=='15' || $mask=='0.1.255.255')
  {
    $netmask = '255.254.0.0';
    $inverse_netmask = '0.1.255.255';
    $cidrbits = 15;
    $valid_mask = 1;
    $numhosts = 131070;
  }
  elseif($mask=='255.252.0.0' || $mask=='14' || $mask=='0.3.255.255')
  {
    $netmask = '255.252.0.0';
    $inverse_netmask = '0.3.255.255';
    $cidrbits = 14;
    $valid_mask = 1;
    $numhosts = 262142;
  }
  elseif($mask=='255.248.0.0' || $mask=='13' || $mask=='0.7.255.255')
  {
    $netmask = '255.248.0.0';
    $inverse_netmask = '0.7.255.255';
    $cidrbits = 13;
    $valid_mask = 1;
    $numhosts = 524286;
  }
  elseif($mask=='255.240.0.0' || $mask=='12' || $mask=='0.15.255.255')
  {
    $netmask = '255.240.0.0';
    $inverse_netmask = '0.15.255.255';
    $cidrbits = 12;
    $valid_mask = 1;
    $numhosts = 1048574;
  }
  elseif($mask=='255.224.0.0' || $mask=='11' || $mask=='0.31.255.255')
  {
    $netmask = '255.224.0.0';
    $inverse_netmask = '0.31.255.255';
    $cidrbits = 11;
    $valid_mask = 1;
    $numhosts = 2097150;
  }
  elseif($mask=='255.192.0.0' || $mask=='10' || $mask=='0.63.255.255')
  {
    $netmask = '255.192.0.0';
    $inverse_netmask = '0.63.255.255';
    $cidrbits = 10;
    $valid_mask = 1;
    $numhosts = 4194302;
  }
  elseif($mask=='255.128.0.0' || $mask=='9' || $mask=='0.127.255.255')
  {
    $netmask = '255.128.0.0';
    $inverse_netmask = '0.127.255.255';
    $cidrbits = 9;
    $valid_mask = 1;
    $numhosts = 8388606;
  }
  elseif($mask=='255.0.0.0' || $mask=='8' || $mask=='0.255.255.255')
  {
    $netmask = '255.0.0.0';
    $inverse_netmask = '0.255.255.255';
    $cidrbits = 8;
    $valid_mask = 1;
    $numhosts = 16777214;
  }
  elseif($mask=='254.0.0.0' || $mask=='7' || $mask=='1.255.255.255')
  {
    $netmask = '254.0.0.0';
    $inverse_netmask = '1.255.255.255';
    $cidrbits = 7;
    $valid_mask = 1;
  }
  elseif($mask=='252.0.0.0' || $mask=='6' || $mask=='3.255.255.255')
  {
    $netmask = '252.0.0.0';
    $inverse_netmask = '3.255.255.255';
    $cidrbits = 6;
    $valid_mask = 1;
  }
  elseif($mask=='248.0.0.0' || $mask=='5' || $mask=='7.255.255.255')
  {
    $netmask = '248.0.0.0';
    $inverse_netmask = '7.255.255.255';
    $cidrbits = 5;
    $valid_mask = 1;
  }
  elseif($mask=='240.0.0.0' || $mask=='4' || $mask=='15.255.255.255')
  {
    $netmask = '240.0.0.0';
    $inverse_netmask = '15.255.255.255';
    $cidrbits = 4;
    $valid_mask = 1;
  }
  elseif($mask=='224.0.0.0' || $mask=='3' || $mask=='31.255.255.255')
  {
    $netmask = '224.0.0.0';
    $inverse_netmask = '31.255.255.255';
    $cidrbits = 3;
    $valid_mask = 1;
  }
  elseif($mask=='192.0.0.0' || $mask=='2' || $mask=='63.255.255.255')
  {
    $netmask = '192.0.0.0';
    $inverse_netmask = '63.255.255.255';
    $cidrbits = 2;
    $valid_mask = 1;
  }
  elseif($mask=='128.0.0.0' || $mask=='1' || $mask=='127.255.255.255')
  {
    $netmask = '128.0.0.0';
    $inverse_netmask = '127.255.255.255';
    $cidrbits = 1;
    $valid_mask = 1;
  }
  else
  {
    $analyze_ip = array();
    debug_msg("200705031703: returning false from analyze_ip($myopts)...");
    return false;
  }
  debug_msg("200604062252: \$netmask = \"$netmask\"",600);
  debug_msg("200604062253: \$inverse_netmask = \"$inverse_netmask\"",600);
  debug_msg("200604062254: \$cidrbits = \"$cidrbits\"",600);
  debug_msg("200604062255: \$valid_mask = \"$valid_mask\"",600);
  $ipaddress = $ip_addr;
  $analyze_ip['ipaddress'] = $ip_addr;
  $analyze_ip['ipaddress_binary'] = ip2bin($ip_addr);

  $ip = ip2long($ip_addr);
  $analyze_ip['ip'] = $ip;

  $nm = ip2long($netmask);
  $analyze_ip['nm'] = $nm;
  
  $analyze_ip['netmask'] = $netmask;
  $analyze_ip['inverse_netmask'] = $inverse_netmask;

  $nw = ($ip & $nm);
  $analyze_ip['nw'] = $nw;

  # NOTE: The calculation of $bc using the below method worked fine for years on a 32-bit OS.
  # When we moved to 64-bit however, the function would sometimes cause incorrect calculations.
  # Therefore, the next chunk of code is commented out on purpose, and has been replaced by Pear Net_IPv4.
  #$bc = $nw | (~$nm);
  #$analyze_ip['bc'] = $bc;

  require_once('Net/IPv4.php');  # See https://pear.php.net/package/Net_IPv4/docs
  $ip_calc = new Net_IPv4();     # create IPv4 object
  $ip_calc->ip = $ip_addr;
  $ip_calc->netmask = $netmask;
  $error = $ip_calc->calculate();
  if(!is_object($error))
  {
    $broadcast_from_ip_calc = $ip_calc->broadcast;
    debug_msg("201506230859: \$broadcast_from_ip_calc = \"$broadcast_from_ip_calc\"");
    $bc = Net_IPv4::ip2double($broadcast_from_ip_calc);
    $analyze_ip['bc'] = $bc;
  }
  else
  {
    $error_msg = $ip_calc_result->getMessage();
    debug_msg("201506230844: \$error_msg = \"$error_msg\"");
    error_message("<br>ERROR: $error_msg",'201506230845');
  }

  $network = long2ip($nw);
  $analyze_ip['network'] = $network;
  $analyze_ip['network_binary'] = ip2bin($network);

  $broadcast = long2ip($bc);
  $analyze_ip['broadcast'] = $broadcast;
  $analyze_ip['broadcast_binary'] = ip2bin($broadcast);

  # Something is wrong with this.
  #$numhosts = ($bc - $nw - 1);
  #if($numhosts == -1)
  #{
  #  $numhosts = NULL;
  #}
  $analyze_ip['numhosts'] = $numhosts;

  if($numhosts == NULL)
  {
    $host_first = NULL;
    $analyze_ip['host_first'] = $host_first;
  }
  else
  {
    $host_first = long2ip($nw+1);
    $analyze_ip['host_first'] = $host_first;
    $analyze_ip['host_first_binary'] = ip2bin($host_first);
  }

  if($numhosts == NULL)
  {
    $host_last = NULL;
    $analyze_ip['host_last'] = $host_last;
    $analyze_ip['host_last_binary'] = ip2bin($host_last);
  }
  else
  {
    $host_last = long2ip($bc-1);
    $analyze_ip['host_last'] = $host_last;
    $analyze_ip['host_last_binary'] = ip2bin($host_last);
  }
  $analyze_ip['cidrbits'] = $cidrbits;


  # Uncomment the following line to turn on debugging for this procedure.
  #debug_analyze_ip();

  if($return_array_of_results)
  {
    debug_msg("200512212052: returning \$analyze_ip...",1000);
    return $analyze_ip;
  }
  return true;
}

function error_message($msg,$abort_with_pointcode=FALSE)
{
  global $colors;
  $red = $colors['red'];
  debug_msg("201308301203: function error_message(\"$msg\",\"$abort_with_pointcode\" START:");
  debug_msg("201308301213: next line is if(is_command_line_version())...");
  if(is_command_line_version())
  {
    debug_msg("201308301214: is_command_line_version() is TRUE");
    echocolor("$msg\n",'red');
    if($abort_with_pointcode)
    {
      script_abort($abort_with_pointcode);
    }
  }
  else
  {
    debug_msg("201308301214: is_command_line_version() is FALSE");
    ech("<font color=\"$red\">$msg</font><br>\n");
    if($abort_with_pointcode)
    {
      debug_msg("201308301145: \$abort_with_pointcode is TRUE");
      debug_msg("201308301146: \$abort_with_pointcode = \"$abort_with_pointcode\"");
      debug_msg("201308301200: next line will call script_abort(\"$abort_with_pointcode\")...");
      script_abort($abort_with_pointcode);
    }
  }
}

function debug_analyze_ip()
{
  global $debug_on,$analyze_ip;
  if ($debug_on)
  {
    debug_msg("200504062159: debug_analyze_ip START");
    if (is_array($analyze_ip))
    {
      debug_msg("200504062201: is_array(\$analyze_ip) is TRUE");
      foreach($analyze_ip as $var => $val)
      {
        debug_msg("200504062204: $var => \"$val\"");
      }
    }
    else
    {
      debug_msg("200504062202: is_array(\$analyze_ip) is FALSE");
      debug_arr($analyze_ip,'analyze_ip');
    }
    debug_msg("200504062203: debug_analyze_IP END");
  }
}

function define_PHP_SELF()
{
  # 20181228T0528Z: somehow I got looking into this function.  It doesn't really make sense to have a function for this.  As far as I know now, this isn't being used anywhere.  Maybe someday eliminate this...
  $PHP_SELF = $_SERVER['PHP_SELF'];
  debug_msg("20050708T0439Z: \$PHP_SELF = \"$PHP_SELF\"");
  if(preg_match("/(.+)\/index.php$/",$PHP_SELF,$hit))
  {
    debug_msg("20060301T1538Z: index.php dropped from PHP_SELF.");
    $new_PHP_SELF = $hit[1].'/';
    debug_msg("20060301T1539Z: \$new_PHP_SELF = \"$new_PHP_SELF\"");
    return $new_PHP_SELF;
  }
  return $PHP_SELF;
}

function dns_name_resolve($hostname)
{
  # this function attempts to resolve $hostname to an ip address.
  # if successful, it will return the ip address
  # if it fails, it will return false.
  $ip = gethostbyname("$hostname");
  if($ip == $hostname)
  {
    return false;
  }
  return $ip;
}

function save_local_copy_of_library_to($localfile,$lines=FALSE)
{
  # This function will save a locally stored copy of the shared library file.
  # Ideally for all scripts which source the gcooper_library.phps file, they
  # will be set up like this:
  #
  # # source my shared library file
  # $local_shared_library_file_filename = '/tmp/.gcooper/php_library/gcooper_library.phps';
  # if(@ include_once("http://www.glencooper.com/php/gcooper_library.phps"))
  # {
  #   save_local_copy_of_library_to("$local_shared_library_file_filename");
  # }
  # elseif(@ include_once("$local_shared_library_file_filename")
  # {
  #   debug_msg("200602072104: successfully loaded shared library file from local file");
  # }
  # else
  # {
  #   echo "ERROR.  Unable to load shared library file from web or local file.<br>\n";
  #   echo "Pointcode: 200602072109.<br>\n";
  #   exit;
  # }
  debug_msg("200602081128: function save_local_copy_of_library_to(\"\$localfile\") START");
  debug_msg("200602081133: \$localfile = \"$localfile\"");

  if(file_exists($localfile))
  {
    debug_msg("200602081132: \$localfile exists.");
    if(!(is_writable($localfile)))
    {
      debug_msg("200602081119: \$localfile is not writable!");
      return FALSE;
    }
  }
  else
  {
    debug_msg("200602081133: \$localfile does not exist.");
    $library_path = dirname($localfile);
    debug_msg("201406021442: \$library_path = \"$library_path\"");
    validate_path($library_path);
  }
  if(!$lines)
  {
    $lines = file("http://www.glencooper.com/php/gcooper_library.txt");
  }
  if(@ !$handle = fopen($localfile,'w'))
  {
    debug_msg("200602081120: unable to open \$localfile for writing!");
    return FALSE;
  }
  if(is_array($lines))
  {
    foreach($lines as $ord => $line)
    {
      if(fwrite($handle,$line) === FALSE)
      {
        debug_msg("200602081121: error writing data to \$localfile!");
        fclose($handle);
        return FALSE;
      }
    }
  }
  fclose($handle);
  debug_msg("200602081122: successfully saved library to \$localfile.");
  return TRUE;
}

function echo_done()
{
  global $colors;
  echo "<font color=\"".$colors['green']."\">Done</font>.<br>\n";
  flush();
}

function filename_part_of_pathname($pathname)
{
  # This function will return only the filename part of the given pathname.
  # For example, if you were to pass it "/tmp/somepath/somefile", it would
  # return "somefile".
  $parts = preg_split("/\//",$pathname);
  $parts_count = count($parts);
  debug_msg("200602081515: \$parts_count = $parts_count",1000);
  $filename = $parts[$parts_count-1];
  debug_msg("200602081519: \$filename = \"$filename\"",1000);
  return $filename;
}

function select_database()
{
  # arg(0) = $which_database
  # arg(1) = OPTIONAL: If true, script will hard abort on error.
  #                    If false, this function will return false on error.
  # 
  # This function will attempt to connect to the mysql database,
  # and select 
  #
  global $mysqlstuff;
  $which_database = func_get_arg(0);
  debug_msg("200602222304: function select_database(\"$which_database\") START:",300);
  $num_args = func_num_args();
  debug_msg("200602222318: \$num_args = \"$num_args\"",500);
  if($num_args > 1)
  {
    $abort_on_error = func_get_arg(1);
    debug_msg("200602222324: \$abort_on_error = \"$abort_on_error\"",1000);
  }
  debug_msg("200602222313: next line will call connect_to_mysql_database()...",300);
  if(!($db = connect_to_mysql_database($mysqlstuff)))
  {
    if($abort_on_error)
    {
      $msg = "ERROR: Unable to connect to MySQL database\n";
      if(!(is_command_line_version()))
      {
        echo "<br>\n";
      }
      script_aborted("200602222320");
    }
    return false;
  }
  debug_msg("200602222325: successfully connected to mysql database.",100);
  debug_msg("200602222305: next line is \$db_selected = mysql_select_db(\"$which_database\")...",1000);
  $db_selected = mysql_select_db("$which_database",$db);
  debug_msg("200602222353: checking to see if \$db_selected is TRUE...",1000);
  if($db_selected)
  {
    debug_msg("200602222306: \$db_selected is TRUE",1000);
    debug_msg("200602222307: returning TRUE from select_database(\"$which_database\")",300);
    return TRUE;
  }
  debug_msg("200602222308: \$db_selected is FALSE",1000);
  if($abort_on_error)
  {
    echo "ERROR.  Unable to select MySQL database \"$which_database\".\n";
    if(!(is_command_line_version))
    {
      echo "<br>\n";
    }
    script_aborted("200602222350");
  }
  debug_msg("200602222309: returning FALSE from select_database(\"$which_database\")",300);
  return FALSE;
}


function connect_to_mysql_database()
{
  $mysqlstuff = FALSE;
  $num_args = func_num_args();
  if($num_args>0)
  {
    debug_msg("200810011507: \$num_args is greater than 0.",500);
    $mysqlstuff = func_get_arg(0);
    #debug_msg("200810011546: what does the \$mysqlstuff array look like?");
    #debug_arr($mysqlstuff,'mysqlstuff');
  }
  if($num_args>1)
  {
    debug_msg("200810011544: \$num_args is greater than 1.",500);
    $mysql_host = '';
    $mysql_user = '';
    $mysql_pass = '';
  }
  debug_msg("200810011545: \$num_args is not greater than 1.",500);
  debug_msg("200602222257: function connect_to_mysql_database(\"\$mysqlstuff\") START:",1000);
  if(!($mysqlstuff))
  {
    echo "<font color=\"red\">WARNING: \$mysqlstuff is FALSE!</font><br>\n";
    echo "<font color=\"red\">Pointcode: 200603011856</font><br><br>\n";
    return;
  }
  debug_msg("200602222258: attempting mysql_connect() using \$mysqlstuff login credentials...",1000);
  $db = @mysql_connect($mysqlstuff['host'],$mysqlstuff['user'],$mysqlstuff['pass']);   # Modified 201603261747 to hush the errors if/when they occur
  if($db)
  {
    debug_msg("200602222259: \$db is TRUE",1000);
    debug_msg("200602222333: returning \$db from connect_to_mysql_database()",1000);
    return($db);
  }
  else
  {
    debug_msg("200602222300: \$db is FALSE",1000);
    return false;
  }
}

function define_rts_reason_Words()
{
  # On the FSD:LVC form, when a LVC is set to Return To Sales status, a selection must be made from the "Reason for Return To Sales" list.
  $words = array();
  $words[0] = 'Incomplete/Missing IP Questionnaire';
  $words[1] = 'Duplicate Host/LVC';
  $words[2] = 'Known-As Address Conflict';
  $words[3] = 'NAT Pool Conflict';
  $words[4] = 'Unsupported design (Return to Sales Eng)';
  $words[5] = 'Invalid Endpoint';
  $words[6] = 'Multiple KA Ranges';
  $words[7] = 'Missing route';
  $words[8] = 'Incorrect primacy';
  $words[9] = 'Other';
  return $words;
}

function define_LVC_Status_Words($specific_individual_status=FALSE)
{
  # LVC Statuses--------
  # As of Jan 15, 2005, each LVC ticket in Remedy has a numerical Status indicator.
  # Status = "0"  : New/Open
  # Status = "1"  : Implementation
  # Status = "2"  : Customer Hold
  # Status = "4"  : Operational
  # Status = "5"  : Disco Request
  # Status = "6"  : Disconnected
  # Status = "7"  : Encryption Pending
  # Status = "8"  : Notify Customer
  # Status = "9"  : Change Request
  # Status = "10" : Closed
  # Status = "11" : Return to Sales
  # Status = "12" : Pending Device Install
  $Status_words = array();
  $Status_words[0] = 'New/Open';
  $Status_words[1] = 'Implementation';
  $Status_words[2] = 'Customer Hold';
  $Status_words[3] = 'Test';
  $Status_words[4] = 'Operational';
  $Status_words[5] = 'Disco Request';
  $Status_words[6] = 'Disconnected';
  $Status_words[7] = 'Encryption Pending';
  $Status_words[8] = 'Notify Customer';
  $Status_words[9] = 'Change Request';
  $Status_words[10] = 'Closed';
  $Status_words[11] = 'Return To Sales';
  $Status_words[12] = 'Pending Device Install';
  
  # Updated 201203131332: changed the way LVC data is loaded from Remedy so that the Status field value is automatically converted from
  # it's numerical ordinal value to the Status Word equivalent.  In order to make sure this doesn't cause any problems in other scripts,
  # define the status words as keys==value.
  $Status_words['New/Open'] = 'New/Open';
  $Status_words['Implementation'] = 'Implementation';
  $Status_words['Customer Hold'] = 'Customer Hold';
  $Status_words['Test'] = 'Test';
  $Status_words['Operational'] = 'Operational';
  $Status_words['Disco Request'] = 'Disco Request';
  $Status_words['Disconnected'] = 'Disconnected';
  $Status_words['Encryption Pending'] = 'Encryption Pending';
  $Status_words['Notify Customer'] = 'Notify Customer';
  $Status_words['Change Request'] = 'Change Request';
  $Status_words['Closed'] = 'Closed';
  $Status_words['Return To Sales'] = 'Return To Sales';
  $Status_words['Pending Device Install'] = 'Pending Device Install';
  $Status_words['Disconnect'] = 'Disconnect';
  if($specific_individual_status)
  {
    if(isset($Status_words[$specific_individual_status]))
    {
      return $Status_words[$specific_individual_status];
    }
  }
  else
  {
    debug_msg("201412091557: \$specific_individual_status = \"$specific_individual_status\"");
  }
  return $Status_words;
}

function define_Ticket_Status_Words()
{
  # AR:TroubleTicket Statuses------
  # As of Oct 2, 2006, each Trouble Ticket in Remedy has a numerical Status indicator.
  # The ticket status is stored in the TStatus field
  $Trouble_Ticket_Status_Words = array();
  $Trouble_Ticket_Status_Words[0] = 'Work-in-Progress';
  $Trouble_Ticket_Status_Words[1] = 'Pending TNS';
  $Trouble_Ticket_Status_Words[2] = 'Pending Telco';
  $Trouble_Ticket_Status_Words[3] = 'Pending Customer';
  $Trouble_Ticket_Status_Words[4] = 'Pending CC';
  $Trouble_Ticket_Status_Words[5] = 'Pending Equipment Replacement';
  $Trouble_Ticket_Status_Words[6] = 'Monitoring';
  $Trouble_Ticket_Status_Words[7] = 'CBEOS';
  $Trouble_Ticket_Status_Words[8] = 'CBEOD';
  $Trouble_Ticket_Status_Words[9] = 'Resolved';
  $Trouble_Ticket_Status_Words[10] = 'Closed';
  $Trouble_Ticket_Status_Words[11] = 'Cancelled';
  return $Trouble_Ticket_Status_Words;
}

function add_backslashes($advanced_search)
{
  # this function adds whatever backslashes are needed in order to run the advanced search from a command-line rq.pl call
  debug_msg("200604260032: function add_backslashes(\"$advanced_search\") START:",500);
  debug_msg("200604260030: \$advanced_search = \"$advanced_search\"",500);
  $new_adv_search = '(';

  $slashed_adv_search = str_replace('"','\"',$advanced_search);
  debug_msg("200694260040: \$slashed_adv_search = \"$slashed_adv_search\"",500);

  $new_adv_search.= $slashed_adv_search;


  $new_adv_search.= ')';
  debug_msg("200604260031: \$new_adv_search = \"$new_adv_search\",500");
  return $new_adv_search;
}

# NOTE: This function is also in outside_builder.  If I ever modify this here, modify it there too!
function is_base64_encoded($data)
{
  if(preg_match('%^[a-zA-Z0-9/+]*={0,2}$%',$data))
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

function analyze_pools($cfg_lines)
{
  $pool_stuff = array();
  $nat_pool_pattern1 = "/^ip nat pool (\S+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) netmask (\d+\.\d+\.\d+\.\d+)( type (\S+))?/";
  $nat_pool_pattern2 = "/^ip nat pool (\S+) (\d+\.\d+\.\d+\.\d+) (\d+\.\d+\.\d+\.\d+) prefix-length (\d+)( type (\S+))?/";
  $nat_pool_pattern3 = "/^ip nat (inside|outside) source list (\S+) (interface|pool) (\S+)(\s+)?(overload)?/";
  foreach($cfg_lines as $ord => $line)
  {
    debug_msg("200704272259: \$line = \"$line\"",1000);
    if(isset($pool_arr))
    {
      unset($pool_arr);
    }
    if(isset($this_pool_name))
    {
      unset($this_pool_name);
    }
    if(preg_match($nat_pool_pattern1,$line,$hit))
    {
      debug_msg("200704272257: matched nat_pool_pattern1.");
      $pool_arr['name'] = $hit[1];
      $pool_arr['first_glob_ip'] = $hit[2];
      $pool_arr['last_glob_ip'] = $hit[3];
      $pool_arr['netmask'] = $hit[4];
      if(isset($hit[6]))
      {
        if($hit[6])
        {
          $pool_arr['type'] = $hit[6];
        }
      }
    }
    elseif(preg_match($nat_pool_pattern2,$line,$hit))
    {
      debug_msg("200704272258: matched nat_pool_pattern2.");
      $pool_arr['name'] = $hit[1];
      $pool_arr['first_glob_ip'] = $hit[2];
      $pool_arr['last_glob_ip'] = $hit[3];
      $pool_arr['prefix-length'] = $hit[4];
      if($hit[6])
      {
        $pool_arr['type'] = $hit[6];
      }
    }
    elseif(preg_match($nat_pool_pattern3,$line,$hit))
    {
      debug_msg("200704272259: matched nat_pool_pattern3.");
      $this_pool_name = $hit[4];
      debug_msg("200704272256: \$this_pool_name = \"$this_pool_name\"");
      if($pool_stuff)
      {
        foreach($pool_stuff as $ord => $pool_arr)
        {
          $loop_pool_name = $pool_arr['name'];
          if($this_pool_name === $loop_pool_name)
          {
            $pool_stuff[$ord]['whichside'] = $hit[1];
            $pool_stuff[$ord]['source_list'] = $hit[2];
            $pool_stuff[$ord]['int_or_pool'] = $hit[3];
            if(isset($hit[6]))
            {
              if($hit[6])
              {
                $pool_stuff[$ord]['overload?'] = 'Yes';
              }
            }
            break;
          }
        }
        unset($pool_arr);
      }
    }
    if(isset($pool_arr))
    {
      if($pool_arr)
      {
        $pool_stuff[] = $pool_arr;
      }
      else
      {
        debug_msg("200704271951: \$pool_arr is FALSE",1000);
      }
    }
    else
    {
      debug_msg("200807280148: isset(\$pool_arr) is FALSE",1000);
    }
  }
  if($pool_stuff)
  {
    debug_msg("200704231728: what does the \$pool_stuff array look like?");
    debug_arr($pool_stuff,'pool_stuff');
    return $pool_stuff;
  }
}

function record_all_remedy_query_calls($server_name,$cmd,$credential_set)
{
  global $_SERVER,$script_name;

  # This next block of code was added 201503101015 so that we can log which script and/or command issued the remedy_query call (the $origin).
  # If the global variable $script_name is set, then use that in the log.  Otherwise, reconstruct the command issued by
  # analyzing the $_SERVER['argv'] array.
  $whole_command = '';
  if(isset($_SERVER['argv']))
  {
    $argv = $_SERVER['argv'];
    debug_msg("201503101005: what does the \$argv array look like?");
    debug_arr($argv,'argv');
    foreach($argv as $ord => $arg)
    {
      if($ord==0)
      {
        if(!preg_match("/index/",$arg))
        {
          $info = pathinfo($arg);
          $arg = basename($arg,'.'.$info['extension']);  # http://php.net/manual/en/function.basename.php#94026
        }
      }
      $whole_command .= "$arg ";
    }
    $whole_command = rtrim($whole_command);
  }
  debug_msg("201503101009: \$whole_command = \"$whole_command\"");
  if(isset($script_name))
  {
    $origin = $script_name;
  }
  else
  {
    $origin = $whole_command;
  }
  debug_msg("201503101017: \$origin = \"$origin\"");
  
  $recording_enabled = 1;
  
  if($recording_enabled)
  {
    $path = '/tmp/.gcooper/remedy_query_log/';
    validate_path($path);
    $todaydate = date('Ymd');
    $filename = $path.$todaydate.'.log';
    debug_msg("201503090941: \$filename = \"$filename\"");
    if(!file_exists($filename))
    {
      if(touch($filename))  # if touching it was successful
      {
        chmod($filename,0777);
      }
    }
    $hostname = php_uname('n');
    if(preg_match("/rstlcnscweb01/",$hostname))
    {
      $hostname = 'nocweb';
    }
    elseif(preg_match("/rstlcinfjmp01/",$hostname))
    {
      $hostname = 'zeus';
    }
    $mytimestamp = date('YmdHis');
    if(preg_match("/^\/home\/gcooper\/shared\/scripts\/perl\/remedy_query\/(rq\.pl .+)$/",$cmd,$hit))
    {
      $cmd = $hit[1];
    }
    $username = define_username();
    $line = "$mytimestamp,$hostname,$username,$origin,$cmd\n";
    debug_msg("201507150936: \$filename = \"$filename\"");
    debug_msg("201503090929: \$line = \"$line\"");
    if(is_writable($filename))
    {
      if($bytes_written = file_put_contents($filename,$line,FILE_APPEND))
      {
        debug_msg("201503090946: wrote $bytes_written bytes to file $filename.");
      }
      else
      {
        debug_msg("201503090947: ERROR: unable to write to file $filename.");
      }
    }
    else
    {
      debug_msg("201503091006: ERROR: is_writable(\"$filename\") is FALSE!");
    }
  }
}

function remedy_query()
{
  # This function uses the rq.pl script to fetch data from Remedy.
  # It returns the fetched data in a multidimensional array.
  #
  # arg0 : $schema / form name
  # arg1 : $advanced_search
  # arg2 : $verbose_mode.  If true, will echo things like "Accessing Remedy database... Success."
  # arg3 : $nonames.  If true, will pass -no flag to rq.pl script
  # arg4 : array of field names to fetch, if false the default field columns will be returned.  Can also be 'all' to retrieve all fields
  # arg5 : if 1; pass the "-st" argument to rq.pl, this causes the status values to be returned as Status Names
  #        if 2; pass the "-statuswordsv2" to rq.pl, this causes the status values to be returned as Status Names when arg4 contains 'all'
  # arg6 : if true, pass this arg's value as the server name to pass to the rq.pl script
  # arg7 : exit_on_failure, by default the script will exit(1) when a problem is encountered.  Set arg to FALSE to not exit(1) on failure.
  #        also when this is set, the array returned by this function will contain $result['error_message'] = string
  # arg8 : if true, pass the "-base64all" argument to rq.pl, this causes all field values to be base64 encoded and decoded
  # arg9 : if true, this script will simulate a Remedy-inaccessible scenario, and will return $results['error_message'] with details
  # arg10: if true, return the data with each record's key=>value pairs in the same order as the $fields array was passed.  Remedy hands back the data in whatever order it wants.
  # arg11: if true, pass -csenv to rq.pl script which forces the rq.pl script to use env vars REMEDY_SERVER, REMEDY_USERNAME, REMEDY_PASSWORD

  # A note about the following $remedy_server global variable:
  # On 20120201, I had a conf call with Susan Plott, IP Imps, who indicated that we needed to get the LVC Builder Script able to build
  # templates for 'BBS Connection Orders' (that's the form name within Remedy).  The problem at this point is that there are no records
  # yet within that form within the Production Remedy environment.  However, there are several records on rst-arsqa1.  Rather than
  # update every single remedy_query() call within the lvcbuild.php code to pass the $remedy_server value, it's much easier to simply
  # declare a global variable called $remedy_server, and if that variable has a value, have this function use it.
  global $remedy_server;

  # set timezone
  date_default_timezone_set('America/New_York');
  
  debug_msg("200604281637: function remedy_query START");
  $num_args = func_num_args();
  $need_to_abort = FALSE;
  $simulate_remedy_down = FALSE;
  $convert_status_names_v2 = FALSE;
  $pass_csenv = FALSE;
  $same_order = FALSE;    # by default, do not re-sort the results to have the same field->value pairs in the same order as the arg4 $fieldnames
  if($num_args>0)
  {
    $schema = func_get_arg(0);
    debug_msg("200604281638: \$schema = \"$schema\"");
  }
  if($num_args>1)
  {
    $advanced_search = func_get_arg(1);
    debug_msg("200604281639: \$advanced_search = \"$advanced_search\"");
  }
  if($num_args>2)
  {
    $verbose_mode = func_get_arg(2);
    debug_msg("200604281640: \$verbose_mode = \"$verbose_mode\"");
  }
  else
  {
    $verbose_mode = NULL;
  }
  if($num_args>3)
  {
    $nonames = func_get_arg(3);
    debug_msg("200604281641: \$nonames = \"$nonames\"");
  }
  if($num_args>4)
  {
    $fieldnames = func_get_arg(4);
    if(is_array($fieldnames))
    {
      debug_msg("200609052106: what does the \$fieldnames array look like?",500);
      debug_arr($fieldnames,'fieldnames',500);
    }
    else
    {
      debug_msg("200811271202: \$fieldnames = \"$fieldnames\"");
    }
  }
  if($num_args>5)
  {
    $convert_status_names = func_get_arg(5);
    debug_msg("201112131512: \$convert_status_names = \"$convert_status_names\"");
    if($convert_status_names==2)
    {
      debug_msg("201112131502: status values will be converted to status names (v2)");
      $convert_status_names_v2 = TRUE;
    }
    elseif($convert_status_names)
    {
      debug_msg("200807070121: status values will be converted to status names.");
    }
  }
  if($num_args>6)
  {
    if(func_get_arg(6))
    {
      $server_name = func_get_arg(6);
    }
    else
    {
      $server_name = FALSE;
    }
  }
  else
  {
    $server_name = FALSE;
  }
  debug_msg("201508171249: \$server_name = \"$server_name\"");
  if($num_args>7)
  {
    if(func_get_arg(7)===FALSE)
    {
      $exit_on_failure = FALSE;
    }
    else
    {
      $exit_on_failure = TRUE;
    }
  }
  else
  {
    $exit_on_failure = TRUE;
  }
  if($num_args>8)
  {
    if(func_get_arg(8)===FALSE)
    {
      $base64all = FALSE;
    }
    else
    {
      debug_msg("201012311310: setting \$base64all to TRUE...");
      $base64all = TRUE;
    }
  }
  else
  {
    $base64all = FALSE;
  }
  if($num_args>9)
  {
    if(func_get_arg(9)===FALSE)
    {
      $simulate_remedy_down = FALSE;
    }
    else
    {
      $simulate_remedy_down = TRUE;
    }
  }
  if($num_args>10)
  {
    if(func_get_arg(10)===FALSE)
    {
      $same_order = FALSE;
    }
    else
    {
      $same_order = func_get_arg(10);
      debug_msg("201506250014: \$same_order = \"$same_order\"");
    }
  }
  if($num_args>11)
  {
    if(func_get_arg(11))
    {
      $pass_csenv = TRUE;
    }
  }

  if(isset($remedy_server))
  {
    debug_msg("201508171246: isset(\$remedy_server) is TRUE");
    if($remedy_server)
    {
      debug_msg("201508171247: \$remedy_server = \"$remedy_server\"");
      if(!$server_name)
      {
        $server_name = $remedy_server;
        debug_msg("201202011424: \$server_name = \"$server_name\"");
      }
    }
  }
  else
  {
    debug_msg("201508171248: isset(\$remedy_server) is FALSE");
  }
  debug_msg("201508170044: \$server_name = \"$server_name\"");
 
  debug_msg("200604260026: next line will call \$advanced_search = add_backslashes(\"\$advanced_search\")...");
  $advanced_search = add_backslashes($advanced_search);
  debug_msg("200604260027: done with \$advanced_search = add_backslashes(\"\$advanced_search\")...");
  $rq_path = '/home/gcooper/shared/scripts/perl/remedy_query/rq.pl';
  $cmd = $rq_path." ";
  if(isset($nonames))
  { 
    if($nonames)
    {
      $cmd.='-no ';
    }
  }
  if(isset($convert_status_names))
  {
    if($convert_status_names)
    {
      $cmd.='-st ';
    }
  }
  if($convert_status_names_v2)
  {
    $cmd.='-statuswordsv2 ';
  }
  if($base64all)
  {
    $cmd.='-base64all ';
  }
  if($pass_csenv)
  {
    $cmd.='-csenv ';
  }
  if($server_name)
  {
    $cmd.= "-server \"$server_name\" ";
  }
  $cmd.= "-s \"$schema\" -a \"$advanced_search\"";
  if($credential_set = pick_a_credential_set_to_use($pass_csenv))
  {
    $cmd .= ' '.$credential_set;
  }
  if(isset($fieldnames))
  {
    if($fieldnames)
    {
      debug_msg("200609052114: \$fieldnames is TRUE");
      $cmd = insert_fieldnames($fieldnames,$cmd);
    }
  }
  if($simulate_remedy_down)
  {
    debug_msg("201110101128: normally we'd query remedy here, but in this case we're running a Remedy-DOWN simulation.");
    $ret = array();
    $ret['error_message'] = "Remedy-DOWN simulation in effect";
    return $ret;
  }
  debug_msg("200604121349: \$cmd = $cmd");
  record_all_remedy_query_calls($server_name,$cmd,$credential_set);
  $output_lines = run_command($cmd,1,1);

  $record_pattern = "/^RECORD NUMBER == \"(.+)\"$/";
  $single_line_data_pattern = "/^\"(.+)\" => \"(.+)?\"$/";
  debug_msg("200609281953: need to deal with multi-line data fields");
  $multi_line_data_pattern = "/^\"(.+)\" => \"(.+)$/";
  $multi_line_data_pattern_end = "/(.+)?\"$/";
  foreach($output_lines as $ord => $line)
  {
    $line = rtrim($line);
    debug_msg("200807221352: \$line = \"$line\"",1000);
    if($need_to_abort)
    {
      if(preg_match("/Remedy Server: (.+)$/",$line,$hit))
      {
        $aborted_on_Remedy_Server = $hit[1];
        continue;
      }
      elseif(preg_match("/Remedy Username: (.+)$/",$line,$hit))
      {
        $aborted_on_Remedy_Username = $hit[1];
        break;
      }
    }
    if(isset($multi_line_data_field_data))
    {
      if($multi_line_data_field_data)
      {
        debug_msg("200609282012: \$multi_line_data_field_data is TRUE");
        if(preg_match($multi_line_data_pattern_end,$line,$hit))
        {
          debug_msg("200609282011: matched multi_line_data_pattern_end.");
          if(isset($hit[1]))
          {
            $multi_line_data_field_data .= $hit[1];
          }
          if($base64all)
          {
            debug_msg("201012311305: \$base64all is TRUE, so decoding the value of the $multi_line_data_field_key field.");
            $multi_line_data_field_data = base64_decode($multi_line_data_field_data);
          }
          elseif(is_base64_encoded($multi_line_data_field_data))
          {
            debug_msg("201009171255: looks like the $multi_line_data_field_key field's value is base64 encoded, so let's decode it!");
            $multi_line_data_field_data = base64_decode($multi_line_data_field_data);
          }
          else
          {
            debug_msg("201009171338: \$multi_line_data_field_data = \"$multi_line_data_field_data\"");
          }
          $results["$record_num"]["$multi_line_data_field_key"] = $multi_line_data_field_data;
          unset($multi_line_data_field_key);
          unset($multi_line_data_field_data);
        }
        else
        {
          # Updated 201009171527, no longer adding \n
          #$multi_line_data_field_data .= $line . "\n";
          $multi_line_data_field_data .= $line;
        }
      }
    }
    debug_msg("200807221354: isset(\$multi_line_data_field_data) is FALSE",1000);
    if(preg_match($record_pattern,$line,$hit))
    {
      debug_msg("200807221356: matched \$record_pattern!",1000);
      $record_num = $hit[1];
      continue;
    }
    if(preg_match("/User is currently connected from another machine/",$line,$hit))
    {
      $need_to_abort = TRUE;
      $aborted_line = $line;
      continue;
    }
    elseif(preg_match("/Unable to log into Remedy\./",$line,$hit))
    {
      if(is_command_line_version())
      {
        echocolor("ERROR!  Unable to log into Remedy.\n",'light_red');
        if(isset($remedy_server))
        {
          echocolor("Remedy Server: $remedy_server\n",'light_red');
        }
        echocolor("$line\n",'light_red');
        if($exit_on_failure)
        {
          $mytime = date("YmdHis");  # Format: 201008051034
          echocolor("Script aborted.\n",'light_red');
          echocolor("Aborted timestamp: $mytime\n",'light_red');
          echocolor("POINTCODE: 200904020352\n",'light_red');
          exit(1);
        }
        else
        {
          debug_msg("200905012300: \$exit_on_failure is FALSE");
        }
      }
      else
      {
        echo "<font color=\"red\">\n";
        echo "ERROR!  Unable to log into remedy.<br>\n";
        echo "$line<br>\n";
        echo "Script aborted.<br>\n";
        echo "POINTCODE: 200904020350<br>\n";
        echo "</font>\n";
        exit(1);
      }
    }
    if(preg_match($single_line_data_pattern,$line,$hit))
    {
      debug_msg("200609281957: matched \$single_line_data_pattern.",900);
      if(isset($hit[2]))
      {
      }
      else
      {
        $hit[2] = '';
      }
      if($base64all)
      {
        $results["$record_num"]["$hit[1]"] = base64_decode($hit[2]);
      }
      else
      {
        $results["$record_num"]["$hit[1]"] = $hit[2];
      }
      continue;
    }
    if(preg_match($multi_line_data_pattern,$line,$hit))
    {
      debug_msg("200609281956: matched \$multi_line_data_pattern.");
      $multi_line_data_field_key = $hit[1];
      debug_msg("200609282007: \$multi_line_data_field_key = \"$multi_line_data_field_key\"");
      # updated 201009171529, no longer appending \n
      #$multi_line_data_field_data = $hit[2]."\n";
      $multi_line_data_field_data = $hit[2];
    }
  }
  if($need_to_abort)
  {
    if(is_command_line_version())
    {
      echocolor("ERROR!  Unable to query Remedy.\n",'light_red');
      echocolor("$aborted_line\n",'light_red');
      if(isset($aborted_on_Remedy_Server))
      {
        echocolor("Remedy Server: $aborted_on_Remedy_Server\n",'light_red');
      }
      if(isset($aborted_on_Remedy_Username))
      {
        echocolor("Remedy Username: $aborted_on_Remedy_Username\n",'light_red');
      }
      $humantime = date("n/j/Y g:i:s A");
      echocolor("Current date and time: $humantime\n",'light_red');
      if($exit_on_failure)
      {
        echocolor("Script aborted.\n",'light_red');
        echocolor("POINTCODE: 200902041020\n",'light_red');
        exit(1);
      }
      else
      {
        debug_msg("201508171658: \$exit_on_failure is FALSE");
      }
    }
    else
    {
      echo "<font color=\"red\">\n";
      echo "ERROR!  Unable to query remedy.<br>\n";
      echo "$aborted_line<br>\n";
      if(isset($aborted_on_Remedy_Server))
      {
        echo "Remedy Server: $aborted_on_Remedy_Server<br>\n";
      }
      if(isset($aborted_on_Remedy_Username))
      {
        echo "Remedy Username: $aborted_on_Remedy_Username<br>\n";
      }
      echo "Script aborted.<br>\n";
      echo "POINTCODE: 200902031705<br>\n";
      echo "</font>\n";
      exit(1);
    }
  }
  if(isset($results))
  {
    if($results)
    {
      if($verbose_mode)
      {
        array_unshift($results,"test");
      }
      if($same_order)
      {
        debug_msg("201506241125: \$same_order is TRUE");
        $results = reorder_results($schema,$results,$fieldnames);
      }
      debug_msg("200605280137: what does the \$results array look like?",1000);
      debug_arr($results,'results',1000);
      return $results;
    }
    else
    {
      return FALSE;
    }
  }
}

function reorder_results($form_name,$results,$fieldnames)
{
  debug_msg("201506250022: what does the \$fieldnames array look like? (these may be fieldnums)");
  debug_arr($fieldnames,'fieldnames');
  $new_fieldnames = array();
  foreach($fieldnames as $ord => $fieldname)
  {
    if(preg_match("/^\d+$/",$fieldname))
    {
      debug_msg("201506250101: \$fieldname \"$fieldname\" is numbers only, so we need to look up the corresponding field name for that fieldnum...");
      $new_fieldname = fieldnum_to_fieldname($form_name,$fieldname);
      debug_msg("201506250102: \$new_fieldname = \"$new_fieldname\" for \"$fieldname\"");
      $new_fieldnames[$ord] = $new_fieldname;
    }
    else
    {
      $new_fieldnames[$ord] = $fieldname;
    }
  }
  $fieldnames = $new_fieldnames;
  debug_msg("201506250029: what does the \$fieldnames array look like now that we've translated fieldnums to fieldnames?");
  debug_arr($fieldnames,'fieldnames');
  $new_results = array();
  foreach($results as $recnum => $arr)
  {
    foreach($fieldnames as $fieldname)
    {
      $new_results[$recnum][$fieldname] = $results[$recnum][$fieldname];
    }
  }
  debug_msg("201506250113: what does the \$new_results array look like?");
  debug_arr($new_results,'new_results');
  return $new_results;
}

function fieldnum_to_fieldname($form_name,$fieldnum)
{
  global $remember_fieldnames;
  if(!isset($remember_fieldnames))
  {
    $remember_fieldnames = array();
  }
  if(!isset($remember_fieldnames[$form_name]))
  {
    $remember_fieldnames[$form_name] = array();
    $filename = lookup_fieldnum_to_fieldname_filename($form_name);
    debug_msg("201506250041: \$filename = \"$filename\"");
  }
  if(isset($remember_fieldnames[$form_name][$fieldnum]))
  {
    return $remember_fieldnames[$form_name][$fieldnum];
  }
  debug_msg("201506250044: loading data from file \"$filename\"...");
  if($lines = file($filename,FILE_IGNORE_NEW_LINES))
  {
    foreach($lines as $line)
    {
      debug_msg("201506250056: \$line = \"$line\"",500);
      if(preg_match("/^ars_GetFieldByName\(\"(\S+)\"\) \= \"(\S+)\"$/",$line,$hit))
      {
        $fieldname = $hit[1];
        debug_msg("201506250057: \$fieldname = \"$fieldname\"",1000);
        $the_fieldnum = $hit[2];
        debug_msg("201506250058: \$the_fieldnum = \"$the_fieldnum\"",1000);
        $remember_fieldnames[$form_name][$the_fieldnum] = $fieldname;
      }
    }
    debug_msg("201506250059: what does the \$$remember_fieldnames[$form_name] array look like?",1000);
    debug_arr($remember_fieldnames[$form_name],"remember_fieldnames[$form_name]",1000);
    if(isset($remember_fieldnames[$form_name][$fieldnum]))
    {
      return $remember_fieldnames[$form_name][$fieldnum];
    }
    else
    {
      return $fieldnum;
    }
  }
}

function lookup_fieldnum_to_fieldname_filename($form_name)
{
  global $remember_fieldname_map_data;
  $path = '/home/gcooper/shared/scripts/perl/remedy_query/cache/';
  if(!(isset($remember_fieldname_map_data)))
  {
    $remember_fieldname_map_data = array();
  }
  if(isset($remember_fieldname_map_data[$form_name]))
  {
    debug_msg("201506250049: we already know the filename for $form_name is ".$remember_fieldname_map_data[$form_name]);
    return $remember_fieldname_map_data[$form_name];
  }
  $map_filename = $path.'cache_filenames';
  if(file_exists($map_filename))
  {
    debug_msg("201506250038: loading map file \"$map_filename\"...");
    if($lines = file($map_filename,FILE_IGNORE_NEW_LINES))
    {
      foreach($lines as $line)
      {
        if(preg_match("/^Schema=\"(\S+)\" CacheFilename=\"(\S+)\"$/",$line,$hit))
        {
          $schema = $hit[1];
          $cache_filename = $hit[2];
          $filename = $path.$cache_filename;
          $remember_fieldname_map_data[$schema] = $filename;
        }
      }
      if(isset($remember_fieldname_map_data[$form_name]))
      {
        debug_msg("201506250047: returning \"$filename\" from lookup_fieldnum_to_fieldname_filename(\"$form_name\")...");
        return $remember_fieldname_map_data[$form_name];
      }
    }
  }
}

function insert_fieldnames($fieldnames,$cmd)
{
  debug_msg("200701301904: function insert_fieldnames(\"$fieldnames\",\"$cmd\") START:",200);
  $cmd.= " -f";
  if(is_array($fieldnames))
  {
    foreach($fieldnames as $ord => $fieldname)
    {
      $cmd.=" \"$fieldname\"";
    }
  }
  elseif($fieldnames==='all')
  {
    $cmd.=" all";
  }
  return $cmd;
}

function pick_a_credential_set_to_use($pass_csenv=FALSE)
{
  if($pass_csenv)
  {
    return '-csenv';
  }
  $hostname = php_uname('n');
  debug_msg("201503101524: \$hostname = \"$hostname\"");
  if(preg_match("/rstlcnscweb01/",$hostname))
  {
    $credential_sets = array(1,2);
    $credential_set_to_use = $credential_sets[array_rand($credential_sets)];
    $credential_set_to_use = '-cs'.$credential_set_to_use;
    debug_msg("201503101526: \$credential_set_to_use = \"$credential_set_to_use\"");
    return $credential_set_to_use;
  }
}

function sleeping_seconds($seconds,$script_name,$skip_record_glenbot_alive=FALSE)
{
  $mypid = getmypid();
  for($i=0;$i<$seconds;$i++)
  {
    $secs_left = $seconds-($i+1);
    $seconds_word = 'second';
    if($secs_left>1)
    {
      $seconds_word .= 's';
    }
    if($skip_record_glenbot_alive)
    {
      # $skip_record_glenbot_alive is TRUE, so do not call record_glenbot_alive.
    }
    else
    {
      record_glenbot_alive($script_name,0,0,$mypid,"Sleeping for $secs_left $seconds_word");
    }
    debug_msg("201401142131: sleeping for $secs_left $seconds_word");
    sleep(1);
  }
}

function remedy_down_sleep_alert($seconds,$loop_counter,$max_retries,$lvcnum)
{
  $loop_counter++;
  if($loop_counter>$max_retries)
  {
    send_crash_email('200910281007',$lvcnum);
    script_abort('201208291605');
  }
  if(is_command_line_version())
  {
    echocolor("ALERT: Unable to query Remedy.  Max Retries = \"$max_retries\"\n",'yellow');
    echocolor("Sleeping $seconds seconds... ",'yellow');
  }
  else
  {
    echo "<font color=\"yellow\">ALERT: Unable to query Remedy.  Max Retries = \"$max_retries\"<br>\n";
    echo "<font color=\"yellow\">sleeping $seconds seconds... </font>";
  }
  sleep($seconds);
  if(is_command_line_version())
  {
    echocolor("Waking up, trying again!\n",'light_green');
  }
  else
  {
    echo "<font color=\"green\">Waking up, trying again!</font><br>\n";
  }
  return $loop_counter;
}

function send_admin_email($txt,$pointcode=FALSE,$script_name,$email_to=FALSE,$no_encountered_line=FALSE)
{
  $hostname = define_hostname_short();
  $args_string = '';
  echocolor("Sending admin email... ",'light_red');
  $temp_filename = tempnam('/tmp/','temp_file_');
  debug_msg("201208241320: \$temp_filename = \"$temp_filename\"");
  $email_lines = array();
  if(!$no_encountered_line)
  {
    $email_lines[] = "The $script_name script running on $hostname encountered an error.";
    $email_lines[] = '';
  }
  foreach($txt as $ord => $line)
  {
    $email_lines[] = "$line\n";
  }
  if($handle = fopen($temp_filename,'w'))
  {
    foreach($email_lines as $ord => $email_line)
    {
      if(fwrite($handle,"$email_line\r\n")===FALSE)
      {
        echocolor("ERROR: Problem occurred when trying to write line to $temp_filename!\n",'light_red');
      }
    }
  }
  fclose($handle);
  $subject = "$script_name Error";
  if($pointcode)
  {
    $subject.= ", POINTCODE: $pointcode";
  }
  if(!$email_to)
  {
    $email_to = 'Glen.Cooper@tnsi.com';
  }
  $output = run_command("php -q /home/gcooper/shared/scripts/php/sendemail/sendemail.php -to=\"$email_to\" -from=\"$script_name Script <Glen.Cooper@tnsi.com>\" -subject=\"$subject\" -bodyfile=\"$temp_filename\"",1,1);
  unlink($temp_filename);
  echocolor("Done.\n",'light_red');
}

function define_Device_Status_Words($specific_individual_status=FALSE)
{
  # DEVICE Statuses--------
  # As of Feb 11, 2005, each DEVICE ticket in Remedy has a numerical DeviceStatus indicator.
  #  DeviceStatus = "0"  : New
  #  DeviceStatus = "1"  : Designed
  #  DeviceStatus = "2"  : Configured
  #  DeviceStatus = "3"  : Shipped
  #  DeviceStatus = "4"  : Installed
  #  DeviceStatus = "5"  : Testing
  #  DeviceStatus = "6"  : Operational
  #  DeviceStatus = "7"  : Pend De-Install
  #  DeviceStatus = "8"  : De-Installed
  #  DeviceStatus = "9"  : Order Cancelled
  #  DeviceStatus = "10" : Hold
  #  DeviceStatus = "11" : Ready
  # As of Apr 28, 2006, a new DEVICE status value was added.
  #  DeviceStatus = "12" : Customer Hold
  # As of Mar 30, 2007, a new DEVICE status value was added.
  #  DeviceStatus = "13" : DR Ready
  $DeviceStatus_words = array();
  $DeviceStatus_words[0] = 'New';
  $DeviceStatus_words[1] = 'Designed';
  $DeviceStatus_words[2] = 'Configured';
  $DeviceStatus_words[3] = 'Shipped';
  $DeviceStatus_words[4] = 'Installed';
  $DeviceStatus_words[5] = 'Testing';
  $DeviceStatus_words[6] = 'Operational';
  $DeviceStatus_words[7] = 'Pend De-Install';
  $DeviceStatus_words[8] = 'De-Installed';
  $DeviceStatus_words[9] = 'Order Cancelled';
  $DeviceStatus_words[10] = 'Hold';
  $DeviceStatus_words[11] = 'Ready';
  $DeviceStatus_words[12] = 'Customer Hold';
  $DeviceStatus_words[13] = 'DR Ready';
  $DeviceStatus_words[14] = 'Pending Installation';
  #
  # Added 201005180243 due to some coding bugs scattered about in the LVC Builder Script
  #
  $DeviceStatus_words['New'] = 'New';
  $DeviceStatus_words['Designed'] = 'Designed';
  $DeviceStatus_words['Configured'] = 'Configured';
  $DeviceStatus_words['Shipped'] = 'Shipped';
  $DeviceStatus_words['Installed'] = 'Installed';
  $DeviceStatus_words['Testing'] = 'Testing';
  $DeviceStatus_words['Operational'] = 'Operational';
  $DeviceStatus_words['Pend De-Install'] = 'Pend De-Install';
  $DeviceStatus_words['De-Installed'] = 'De-Installed';
  $DeviceStatus_words['Order Cancelled'] = 'Order Cancelled';
  $DeviceStatus_words['Hold'] = 'Hold';
  $DeviceStatus_words['Ready'] = 'Ready';
  $DeviceStatus_words['Customer Hold'] = 'Customer Hold';
  $DeviceStatus_words['DR Ready'] = 'DR Ready';
  $DeviceStatus_words['Pending Installation'] = 'Pending Installation';
  if($specific_individual_status)
  {
    if(isset($DeviceStatus_words[$specific_individual_status]))
    {
      return $DeviceStatus_words[$specific_individual_status];
    }
  }
  return $DeviceStatus_words;
}

function convert_options_to_hidden_form_values($options)
{
  # this function will convert $options, the URL options passed at runtime, to hidden form values meant to
  # be passed back to the script via a html form using method = POST.  The script will return an array of
  # <input type=hidden> strings so they can be echo'ed within a <form method=POST>.
  debug_msg("200601301910: function add_hidden_post_form_values(\"\$options\") START");
  debug_msg("200601301909: \$options = \"$options\"");
  if(!($options))
  {
    return false;
  }
  $option_pairs = explode('&',$options);
  $i = 0;
  $option_pairs_count = count($option_pairs);
  debug_msg("200601301915: \$option_pairs_count = \"$option_pairs_count\"");
  debug_msg("200601301918: what does \$option_pairs look like?");
  debug_arr($option_pairs,'option_pairs');
  while($i<$option_pairs_count)
  {
    $current_pair = $option_pairs[$i];
    debug_msg("200601301920: \$current_pair = \"$current_pair\"");
    $option_pair = split('=',$current_pair);
    $string = "<input type=\"hidden\" name=\"".$option_pair[0]."\" value=\"".$option_pair[1]."\">\n";
    $output_lines[] = $string;
    $i++;
  }
  $output_lines_count = count($output_lines);
  debug_msg("200601301919: \$output_lines_count = \"$output_lines_count\"");
  debug_msg("200601301914: what does \$output_lines look like?");
  debug_arr($output_lines,'output_lines',NULL,1);
  return $output_lines;
}

function remedy_timestamp($timestamp)
{
  # This function takes a unix $timestamp value and converts it to the same
  # human readable datetime format that Remedy shows in ARUser.
  $remedy_timestamp = date("m/j/Y h:i:s A",$timestamp);
  return $remedy_timestamp;
}

function define_log_filename($path='')
{
  global $last_garbage_collection_date;
  # NOTE: Always include trailing slash with specified path!
  $mypid = getmypid();
  if(!$path)
  {
    echo "ERROR: No path specified within define_log_filename function!\n";
    exit;
  }
  $today = date("Ymd");  # will produce date like "20100406".
  debug_msg("201004131232: \$today = \"$today\"");
  $log_output_to_filename = $path.$today.".$mypid";
  if(!isset($last_garbage_collection_date))
  {
    $last_garbage_collection_date = 'never';
  }
  if(!($last_garbage_collection_date===$today))
  {
    debug_msg("201004131128: time to take out the trash!");
    $last_garbage_collection_date = $today;
    if(take_out_the_trash($path))
    {
      debug_msg("201004131129: done taking out the trash");
    }
    else
    {
      debug_msg("201004131221: take_out_the_trash(\"$path\") is FALSE");
    }
  }
  return $log_output_to_filename;
}

if(!function_exists('echocolor'))
{
  function echocolor($text,$color="normal",$back=FALSE,$log_output=FALSE,$only_echo_once=FALSE)
  {
    global $nocolor,$colors,$log_echocolor_to_file_path,$echocolor_no_echo,$remember_echocolored;

    # If you do not want echocolor to echo anything, set the global variable $echocolor_no_echo to TRUE
    if(!isset($echocolor_no_echo))
    {
      $echocolor_no_echo = FALSE;
    }
    if($echocolor_no_echo)
    {
      $back = TRUE;
    }

    # If you only want something echo'ed ONCE per script run, pass $only_echo_once a true value.
    if($only_echo_once)
    {
      debug_msg("201602291509: \$only_echo_once is TRUE");
      if(!isset($remember_echocolored))
      {
        $remember_echocolored = array();
      }
      if(in_array(rtrim($text),$remember_echocolored))
      {
        debug_msg("201602291507: already echo'ed \"$text\", not echo'ing it again");
        return;
      }
      else
      {
        debug_msg("201602291510: adding \"".rtrim($text)."\" to \$remember_echocolored array...");
        $remember_echocolored[] = rtrim($text);
      }
    }

    if(is_command_line_version())
    {
      if(isset($nocolor))
      {
        if($nocolor)
        {
          echo $text;
          return;
        }
      }
      $cmdline_colors = array('light_red'  => "[1;31m", 'light_green' => "[1;32m", 'yellow'     => "[1;33m",
                              'light_blue' => "[1;34m", 'magenta'     => "[1;35m", 'light_cyan' => "[1;36m",
                              'white'      => "[1;37m", 'normal'      => "[0m",    'black'      => "[0;30m",
                              'red'        => "[0;31m", 'green'       => "[0;32m", 'brown'      => "[0;33m",
                              'blue'       => "[0;34m", 'cyan'        => "[0;36m", 'bold'       => "[1m",
                              'underscore' => "[4m",    'reverse'     => "[7m"   , 'NONE'       => 'NONE');
      if(isset($cmdline_colors["$color"]))
      {
      if($color==='NONE')
        {
          # When this happens, do not echo anything, just return.
          return;
        }
        else
        {
          $out = $cmdline_colors["$color"];
          $ech = chr(27)."$out".$text.chr(27)."[0m";
        }
      }
      else
      {
        $ech = $text;
      }
      if($back)
      {
        return $ech;
      }
      else
      {
        echo $ech;
        if($log_echocolor_to_file_path)
        {
          $log_output_to_filename = define_log_filename($log_echocolor_to_file_path);
          if(!$handle = fopen($log_output_to_filename,'a'))
          {
            echo "ERROR: Unable to open logfile \"$log_output_to_filename\" for appending!\n";
            echo "Script aborted.\n";
            echo "POINTCODE: 20100406T1604Z\n";
            exit;
          }
          if(fwrite($handle,$ech)===FALSE)
          {
            echo "ERROR.  Unable to write data to logfile \"$log_output_to_filename\".\n";
            echo "Script aborted.\n";
            echo "POINTCODE: 20100406T1106Z\n";
            exit;
          }
          fclose($handle);
        }
      }
    }
    else  # command_line_version is false, so we're in a webpage
    {
      $text = str_replace("\n","<br>\n",$text);
      $color = map_cmd_line_colors_to_webpage_colors($color);
      echo "<font color=\"$color\">$text</font>";
    }
  }
}

function map_cmd_line_colors_to_webpage_colors($color)
{
  # For most of my command-line scripts, light_blue is used for normal text, whereas in a browser, white is used.
  # This function will translate an echocolor's color to html color.
  global $colors;
  if($color==='light_blue')
  {
    $color = '#ffffff';
  }
  elseif($color==='light_green')
  {
    $color = $colors['green'];
  }
  elseif($color==='red')
  {
    $color = $colors['red'];
  }
  elseif($color==='light_red')
  {
    $color = $colors['red'];
  }
  elseif($color==='yellow')
  {
    $color = $colors['yellow'];
  }
  elseif($color==='white')
  {
    $color = "#FFFFFF";
  }
  return $color;
}

function split_octets($ip)
{
  # this function, when supplied with an $ip, will return an array of that ip's octets, or an array of null's if it's not an ip
  $pattern = '/(\d+)\.(\d+)\.(\d+)(\.)?(\d+)?/';
  if(preg_match($pattern,$ip,$matches))
  {
    return array($matches[0],$matches[1],$matches[2],$matches[3],$matches[5]);
  }
  return array(NULL,NULL,NULL,NULL,NULL);
}

function cisco_config_section($cfg_lines,$start_line)
{
  # This function will return the config lines of the section of the config of $rtr that begins with $start_line.
  debug_msg("20060427T2218Z: function cisco_config_section(\"\$cfg_lines\",\"$start_line\") START:");
  $config_lines_count = count($cfg_lines);
  debug_msg("20060527T2019Z: \$config_lines_count = \"$config_lines_count\"");
  $start_line_num = FALSE;
  $start_line_search = str_replace(' ','\s',$start_line);
  $start_line_search = str_replace('/','\/',$start_line_search);
  $start_line_search = str_replace('-','\-',$start_line_search);
  $start_line_search = str_replace('>','\>',$start_line_search);
  $start_line_search = str_replace('<','\<',$start_line_search);
  debug_msg("20140228T1621Z: \$start_line_search = \"$start_line_search\"");
  for($i=0;$i<$config_lines_count;$i++)
  {
    $cfg_line = rtrim($cfg_lines[$i]);
    debug_msg("20060527T2232Z: config_line # $i: \"$cfg_line\"",1000);
    if(preg_match("/^$start_line_search$/",rtrim($cfg_lines[$i]),$hit))
    {
      $start_line_num = $i;
      debug_msg("20060527T2213Z: \$start_line_num = \"$start_line_num\"");
      break;
    }
  }
  if(!($start_line_num))
  {
    debug_msg("20060428T1419Z: \$start_line_num is FALSE.");
    debug_msg("20140228T1037Z: Returning FALSE from cisco_config_section(\"\$cfg_lines,\"$start_line\")...");
    return FALSE;
  }
  for($i=($start_line_num+1);$i<$config_lines_count;$i++)
  {
    if(preg_match("/^!$/",rtrim($cfg_lines[$i]),$hit))
    {
      $end_line_num = $i;
      debug_msg("20060527T2238Z: \$end_line_num = \"$end_line_num\"");
      break;
    }
  }
  for($i=$start_line_num;$i<$end_line_num;$i++)
  {
    $section_lines[] = rtrim($cfg_lines[$i]);
  }
  debug_msg("20060527T2248Z: what does the \$section_lines array look like?");
  debug_arr($section_lines,'section_lines');
  return $section_lines;
}

function ech()
{
  # arg 0 = $text            : what to echo
  # arg 1 = $verbose_level   : (OPTIONAL)
  # arg 2 = $hush_only       : (OPTIONAL)
  # arg 3 = $dont_pad        : (OPTIONAL), if true, no padding will be added
  #
  # This function replaces the standard "echo" command.  It accepts an
  # optional parameter: $verbose_level.
  # If the $lvcinfo_array2['hush_level'] is set higher than $verbose_level,
  # then the text will not be echo'ed.  If the $verbose_level is not specified,
  # $verbose_level will be set to zero.
  #
  # For arg2, if TRUE, the $text will only be echoed if the
  # $lvcinfo_array2['hush_level'] is set lower than $verbose_level.

  global $lvcinfo_array2;
  if(isset($lvcinfo_array2['hush_level']))
  {
    $hush_level = $lvcinfo_array2['hush_level'];
  }
  else
  {
    $hush_level = 0;
  }
  if(isset($lvcinfo_array2['no_pads']))
  {
    $no_pads = $lvcinfo_array2['no_pads'];
  }
  else
  {
    $no_pads = 0;
  }
  debug_msg("20060223T0212Z: \$hush_level = \"$hush_level\"",500);
  $num_args = func_num_args();
  $text = func_get_arg(0);
  #if(is_command_line_version())
  #{
  #  $text = strip_tags($text);
  #}
  if($num_args>1)
  {
    $verbose_level = func_get_arg(1);
  }
  else
  {
    $verbose_level = 0;
  }
  debug_msg("20070417T1455Z: \$verbose_level = \"$verbose_level\"",500);
  if($num_args>2)
  {
    $hush_only = func_get_arg(2);
  }
  else
  {
    $hush_only = 0;
  }
  if($num_args>3)
  {
    $dont_pad = func_get_arg(3);
    #debug_msg("20081103T1513Z: \$dont_pad = \"$dont_pad\"");
  }
  else
  {
    $dont_pad = 0;
  }
  if($no_pads)
  {
    $dont_pad = 1;
  }
  if($hush_level <= $verbose_level)
  {
    if($hush_only)
    {
      # do not echo
    }
    else
    {
      echo "$text";
      if($dont_pad)
      {
        #debug_msg("20081103T1511Z: \$dont_pad is TRUE");
      }
      else
      {
        for($i=0;$i<100;$i++)
        {
          echo "\n<!-- padding extra data to fill flush buffer -->";
        }
      }
      flush();
    }
  }
  else
  {
    if($hush_only)
    {
      if($dont_pad)
      {
        #debug_msg("20081103T1512Z: \$dont_pad is TRUE");
      }
      else
      {
        for($i=0;$i<50;$i++)
        {
          echo "\n<!-- padding extra data to fill flush buffer -->";
        }
      }
      echo $text;
      flush();
    }
  }
}

function tripwire($pointcode)
{
  global $mysqlstuff,$glob;
  debug_msg("20060528T0319Z: function tripwire(\"$pointcode\") START");
  $tripwire_table_name = $glob['tripwire_table_name'];
  debug_msg("20150115T1050Z: what does the \$mysqlstuff array look like?",1000);
  debug_arr($mysqlstuff,'mysqlstuff',1000);
  if(!(connect_to_mysql_database($mysqlstuff)))
  {
    return false;
  }
  if(!(select_database("ipdesk")))
  {
    debug_msg("20060417T2107Z: returning false",500);
    return false;
  }

  # if the table doesn't already exist, create it by doing:
  # CREATE TABLE `tripwire` (
  # `pointcode` BIGINT NOT NULL ,
  # `count` BIGINT NOT NULL ,
  # PRIMARY KEY ( `pointcode` )
  # );

  $sql = "SELECT * FROM $tripwire_table_name WHERE pointcode=\"$pointcode\"";
  debug_msg("20060528T0308Z: \$sql= $sql");
  $result = mysql_query($sql);
  if($result)
  {
    debug_msg("20060528T0309Z: \$result is TRUE");
  }
  else
  {
    debug_msg("20060528T0310Z: \$result is FALSE");
    return false;
  }
  $num_rows = mysql_num_rows($result);
  debug_msg("20060528T0311Z: \$num_rows = \"$num_rows\"");
  if($num_rows)
  {
    $sql = "UPDATE $tripwire_table_name SET count=count+1 WHERE pointcode=\"$pointcode\"";
  }
  else
  {
    $sql = "INSERT INTO $tripwire_table_name VALUES (\"$pointcode\",1)";
  }
  debug_msg("20060428T0341Z: \$sql= $sql;");
  unset($result);
  $result = mysql_query($sql);
  if($result)
  {
    debug_msg("20060528T0339Z: \$result is TRUE");
  }
  else
  {
    debug_msg("20060528T0340Z: \$result is FALSE");
    return false;
  }
}

function table_exists($tablename, $database = false)
{
  # borrowed from http://www.electrictoolbox.com/check-if-mysql-table-exists/php-function/
  if(!$database)
  {
    $res = @mysql_query("SELECT DATABASE()");
    $database = @mysql_result($res, 0);
  }
  $res = @mysql_query("
     SELECT COUNT(*) AS count 
     FROM information_schema.tables 
     WHERE table_schema = '$database' 
     AND table_name = '$tablename'
  ");
  if($res)
  {
    return mysql_result($res, 0) == 1;
  }
}

function h1($text)
{
  echo "<center>\n";
  echo "<h1>$text</h1>\n";
  echo "</center>\n";
}


function in_multi_array($search_str, $multi_array)
{
  global $debug_on,$colors;
  debug_msg("20050504T1532Z: function in_multi_array START");
  if(!(is_array($multi_array)))
  {
    debug_msg("20050504T1538Z: is_array is FALSE.  Returning 0...");
    return 0;
  }
  if(in_array($search_str, $multi_array))
  {
    debug_msg("20050504T1536Z: in_array is TRUE.  Returning 1...");
    return 1;
  }
  foreach($multi_array as $key => $value)
  {
    if(is_array($value))
    {
      debug_msg("20050504T1539Z: is_array($value) is TRUE.");
      $found = in_multi_array($search_str, $value);
      if($found)
      {
        debug_msg("20050504T1535Z: \$found is TRUE.  Returning 1...");
        return 1;
      }
    }
    else
    {
      if($key === $search_str)
      {
        debug_msg("20050504T1534Z: \$key==\$search_str // \"$key\"==\"$search_str\".");
        return 1;
      }
    }
  }
  return 0;
}

function construct_remedy_result_table($results,$schema,$columns,$center,$echo_lines,$color_mode)
{
  # This function draws a table of results from a remedy_query.

  # arg0 : $data - multidimensional array of data as returned by remedy_query
  # arg1 : $schema - need to know which schema the data is from in order to properly convert status values to status words
  # arg2 : $columns - if NULL, all columns will be drawn.  Otherwise specify
  #        the column names you want to have included in the table in the
  #        format of: $arr["Column Header"] = $FieldName
  #        EXAMPLE: 
  # Array
  # (
  #   [000000000005245] => Array
  #       (
  #           [Modified-date] => 1096886316
  #           [DateOperational] => 995151600
  #           [LVCID] => abn11-host-2 <-> jpm07-host-11
  #           [Create-date] => 995056561
  #           [CompletionDate] => 1096585200
  #           [Status] => 6
  #           [Division] => FSD
  #           [LVCTicketNumber] => 000000000005245
  #      )
  #
  #   [USLVC0000035094] => Array
  #      (
  #          [Modified-date] => 1150725984
  # ......
  #
  #  To draw a table with column headers "MODIFIED DATE", "LVC ID", you'd
  #  pass an array with the following data:
  #  $arr["MODIFIED DATE"] = "Modified-date";
  #  $arr["LVC ID"] = "LVCID";
  #
  # arg3 : $center - if TRUE, the table will be encompassed in <center> tags
  # arg4 : $echo_lines - if FALSE, the output lines will be returned in an array
  #                      if TRUE, the table will be echo'ed
  #
  # arg5 : $color_mode = highlight rows according to their status
  #        VALUES:
  #                0 or NULL : no colored rows
  #                1 : Matching LVCs for when doing Change Requests, Operational LVCs will be red.
  #

  global $colors;

  if(!(is_array($results)))
  {
    this_should_never_happen("20060619T2209Z");
  }
  if($schema==='FSD:LVC')
  {
    $StatusWords = define_LVC_Status_Words();
  }
  if($center)
  {
    $output[] = "<center>\n";
  }
  if(!(is_array($columns)))
  {
    foreach($results as $tktnum => $data)
    {
      foreach($data as $column => $value)
      {
        $columns[] = $column;
      }
      break; # no sense in looping thru this array, we just wanted the column names.
    }
  }
  else
  {
    foreach($columns as $column_header => $column)
    {
      $column_headers[] = $column_header;
      $new_columns[] = $column;
    }
    $columns = $new_columns;
  }
  debug_msg("20060619T2309Z: what does the \$columns array look like?");
  debug_arr($columns,'columns');

  # all of the field names below will have their timestamp values converted to a readable format
  $convert_timestamps[] = 'Modified-date';
  $convert_timestamps[] = 'DateOperational';
  $convert_timestamps[] = 'Create-date';
  $convert_timestamps[] = 'CompletionDate';
  
  $output[] = "<table border=2>\n";
  $output[] = "<tr>\n";
  if($column_headers)
  {
    foreach($column_headers as $ord => $column_header)
    {
      $output[] = "<td bgcolor=\"#FFFFFF\"><font color=\"#000000\"><b>$column_header</b></font></td>\n";
    }
  }
  else
  {
    foreach($columns as $ord => $column)
    {
      $output[] = "<td bgcolor=\"#FFFFFF\"><font color=\"#000000\"><b>$column</b></font></td>\n";
    }
  }
  $output[] = "</tr>\n";
  foreach($results as $tktnum => $data)
  {
    $output[] = "<tr>\n";
    debug_msg("20060619T2307Z: \$tktnum = \"$tktnum\"");
    debug_msg("20060619T2308Z: what does the \$data array look like?");
    debug_arr($data,'data');
    foreach($columns as $ord => $column)
    {
      unset($bgcolor);
      if($data["$column"])
      {
        $value = $data["$column"];
        if(in_array($column,$convert_timestamps))
        {
          $value = date("n/j/Y g:i:s A",$value);
        }
        elseif($column==='Status')
        {
          debug_msg("20060619T2350Z: \$column = \"$column\"");
          debug_msg("20060619T2351Z: \$value = \"$value\"");
          debug_msg("20060620T0024Z: \$color_mode = \"$color_mode\"");
          if($color_mode===1)
          {
            debug_msg("20060620T0023Z: \$color_mode is 1.");
            if(($value==='6')||($value==='9'))
            {
              debug_msg("20060620T0021Z: \$value is 6.");
              $bgcolor = $colors['ltgreen'];
              debug_msg("20060620T0033Z: \$bgcolor = \"$bgcolor\"");
            }
            else
            {
              debug_msg("20060620T0022Z: \$value is not 6.");
              $bgcolor = $colors['ltred'];
            }
            
          }
          $value = $StatusWords[$value];
        }
        $output_line = "<td";
        if($bgcolor)
        {
          debug_msg("20060620T0030Z: adding color to cell...");
          $output_line.=" bgcolor=\"$bgcolor\"";
        }
        $output_line.= ">";
        if($bgcolor)
        {
          $output_line.= "<font color=\"#000000\">";
        }
        $output_line.= "$value";
        if($bgcolor)
        {
          $output_line.= "</font>\n";
        }
        $output_line.= "</td>\n";
        $output[] = $output_line;
      }
      else
      {
        $output[] = "<td>&nbsp;</td>\n";
      }
    }
    $output[] = "</tr>\n";
  }
  $output[] = "</table>\n";
  if($center)
  {
    $output[] = "</center>\n";
  }
  if($echo_lines)
  {
    foreach($output as $ord => $line)
    {
      echo $line;
    }
    return $output;
  }
  else
  {
    return $output;
  }
}


function unix_timestamp_of($time_string)
{
  debug_msg("20070410T1119Z: function unix_timestamp_of(\"$time_string\") START:",500);
  if($time_string === 'Wed Dec 31 19:00:00 1969')
  {
    return 0;
  }
  $cmd = "php -q /home/gcooper/shared/scripts/php/timestamp/timestamp.php \"$time_string\"";
  debug_msg("20070410T1203Z: \$cmd = \"$cmd\"",500);
  $output = run_command($cmd,1,1);
  debug_msg("20070410T1204Z: what does the \$output array look like?",100);
  debug_arr($output,'output',100);
  $pattern = "/^Unix Timestamp: (\d+)/";
  foreach($output as $ord => $line)
  {
    if(preg_match($pattern,$line,$hit))
    {
      $unix_timestamp = $hit[1];
      debug_msg("20070410T1120Z: returning \"$unix_timestamp\" from unix_timestamp_of(\"$time_string\")...",100);
      return $unix_timestamp;
    }
  }
  debug_msg("20070410T1204Z: ut oh, this can't be good...");
  this_should_never_happen("20070410T1205Z");
}

function extract_acls_from_cisco_config($config_lines)
{
  # This function will run thru every line of a Cisco config (passed in $config_lines array)
  # and extract all of the access-lists into one multi-dimensional array and then return it.
  debug_msg("20070427T1735Z: function import_acls_into_array START");
  if(!(is_array($config_lines)))
  {
    return false;
  }
  $pattern_numbered = "/^access-list (\d+) ((permit|deny|remark).+)/";
  $pattern_extended_named = "/^ip access-list extended (\S+)/";
  $pattern_good_stuff = "/^ ((permit|deny|remark) .+)$/";
  $pattern_standard_named = "/^ip access-list standard (\S+)/";
  foreach($config_lines as $ord => $line)
  {
    if(preg_match($pattern_numbered,$line,$hit))
    {
      debug_msg("20070427T2218Z: \$line = \"$line\"");
      debug_msg("20070427T2216Z: matched numbered ACL pattern.");
      $aclname = $hit[1];
      debug_msg("20070427T2217Z: \$aclname = \"$aclname\"");
      $permit_or_deny_part = $hit[2];
      if(($aclname>=1 && $aclname <=99) || ($aclname>=1300 && $aclname<=1999))
      {
        $type = 'standard';
      }
      elseif(($aclname>=100 && $aclname<=199) || ($aclname>=2000 && $aclname<=2699))
      {
        $type = 'extended';
      }
      $acl["$aclname"]['type'] = $type;
      $acl["$aclname"]['acl'][] = $permit_or_deny_part;
    }
    elseif(preg_match($pattern_extended_named,$line,$hit))
    {
      $aclname = $hit[1];
      debug_msg("20070427T2213Z: extended \$aclname = \"$aclname\"");
      $type = 'extended';
    }
    elseif(preg_match($pattern_standard_named,$line,$hit))
    {
      $aclname = $hit[1];
      debug_msg("20070423T2214Z: standard \$aclname = \"$aclname\"");
      $type = 'standard';
    }
    elseif(preg_match($pattern_good_stuff,$line,$hit))
    {
      $good_stuff_match = $hit[1];
      debug_msg("20070427T2220Z: \$good_stuff_match = \"$good_stuff_match\"");
      $acl["$aclname"]['type'] = $type;
      $acl["$aclname"]['acl'][] = $good_stuff_match;
    }
  }
  return($acl);
}

function ordinal_suffix($value, $sup = 0)
{
// Function written by Marcus L. Griswold (vujsa)
// Found at http://www.handyphp.com

    #debug_msg("20070428T0111Z: \$value = \"$value\"");
    is_numeric($value) or trigger_error("<b>\"$value\"</b> is not a number!, The value must be a number in the function <b>ordinal_suffix()</b>", E_USER_ERROR);
    if(substr($value, -2, 2) == 11 || substr($value, -2, 2) == 12 || substr($value, -2, 2) == 13){
        $suffix = "th";
    }
    else if (substr($value, -1, 1) == 1){
        $suffix = "st";
    }
    else if (substr($value, -1, 1) == 2){
        $suffix = "nd";
    }
    else if (substr($value, -1, 1) == 3){
        $suffix = "rd";
    }
    else {
        $suffix = "th";
    }
    if($sup){
        $suffix = "<sup>" . $suffix . "</sup>";
    }
    return $value . $suffix;
}

function use_curl()
{
  debug_msg("20070503T1403Z: function use_curl() START");
  # arguments:
  # arg 0: $url
  # arg 1: if TRUE, $result will be an array of lines.
  #        if FALSE, $result will be all of the response in one variable.
  # arg 2: $post_data (if TRUE and array, the values will be sent via POST)
  $num_args = func_num_args();
  debug_msg("20070503T1404Z: \$num_args = \"$num_args\"");
  if($num_args>0)
  {
    $url = func_get_arg(0);
    debug_msg("20070503T1405Z: \$url = \"$url\"");
    $ch = curl_init("$url");
  }
  if($num_args>1)
  {
    $return_array = func_get_arg(1);
    if($return_array)
    {
      debug_msg("20070503T1406Z: results from curl will be returned as an array of result lines.");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    }
  }
  if($num_args>2)
  {
    $post_data = func_get_arg(2);
    if($post_data)
    {
      if(is_array($post_data))
      {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
      }
    }
  }
  else
  {
    debug_msg("20070503T1407Z: \$post_data is FALSE");
  }
  if($url)
  {
    $result = curl_exec($ch);
    curl_close($ch);
    if($return_array)
    {
      $result_lines = preg_split("/\n/",$result);
      return $result_lines;
    }
    else
    {
      return $result;
    }
  }
}

function trim_array($arr)
{
  # This function will trim every line in an array, and remove any
  # elements which have no data.
  if(is_array($arr))
  {
    foreach($arr as $ord => $line)
    {
      $trimmed_line = rtrim($line);
      if($trimmed_line)
      {
        $stuff[] = $trimmed_line;
      }
    }
    return $stuff;
  }
}

function validate_path($path)
{
  # This function checks each directory in $path to see if it exists, and is readable.  If it does not exist,
  # the validate_dir fuction will attempt to create it with permissions 777 (drwxrwxrwx).
  debug_msg("20080708T0252Z: function validate_path(\"$path\") START:",1000);
  $dirname = '';
  $dirs = split('/',$path);
  if(is_array($dirs))
  {
    debug_msg("20080702T1612Z: what does the \$dirs array look like?",1000);
    debug_arr($dirs,'dirs',1000);
    foreach($dirs as $ord => $dir)
    {
      if($dir)
      {
        debug_msg("20080705T0457Z: \$dir = \"$dir\"",1000);
        $dirname .= "/" . $dir;
        debug_msg("20080705T0458Z: \$dirname = \"$dirname\"",1000);
        if(validate_dir($dirname))
        {
          $all_directories_validated = 1;
        }
        else
        {
          $all_directories_validated = 0;
          break;
        }
      }
    }
    if($all_directories_validated)
    {
      debug_msg("20080708T0336Z: \$all_directories_validated is TRUE",1000);
      return true;
    }
    else
    {
      debug_msg("20080708T0337Z: \$all_directories_validated is FALSE",1000);
      return true;
    }
  }
}

function validate_dir($dir)
{
  global $colors;
  $red = $colors['red'];
  # this function checks to see if $dir exists.  if not, it will create it with permissions 777 (drwxrwxrwx)
  if(is_dir($dir))
  {
    debug_msg("20080702T1523Z: is_dir(\"$dir\") is TRUE",1000);
    if(is_readable($dir))
    {
      debug_msg("20080702T1534Z: is_readable(\"$dir\") is TRUE",1000);
      return true;
    }
    else
    {
      debug_msg("20080702T1534Z: is_readable(\"$dir\") is FALSE",1000);
      return FALSE;
    }
  }
  else
  {
    debug_msg("20080702T1523Z: is_dir(\"$dir\") is FALSE",1000);
    if(file_exists($dir))
    {
      ech("<font color=\"red\">ERROR.  \"$dir\" is supposed to be a directory, but there's already a file by that name.</font><br>\n");
      script_abort("20080702T1519Z");
    }
    else
    {
      debug_msg("20080702T1651Z: \$dir = \"$dir\"",1000);
      $pattern = "/^(\/.+)\/.+$/";
      if(preg_match($pattern,$dir,$hit))
      {
        $before_path = $hit[1];
        debug_msg("20080702T1655Z: \$before_path = \"$before_path\"",1000);
        if(is_writable($before_path))
        {
          debug_msg("20080702T1700Z: is_writable(\"$before_path\") is TRUE",1000);
        }
        else
        {
          ech("<font color=\"$red\">ERROR.  This script needs to be able to write to the following path:<br>\n");
          ech("$before_path<br>\n");
          ech("It appears the above path exists, but is not writable by the username that this script is running under.<br>\n");
          ech("Unless something major has changed since the LVC Builder Script was created, the PHP process should run under the username \"apache\".<br>\n");
          ech("</font>\n");
          ech("POINTCODE: 20080708T0248Z<br>\n");
          exit;
        }
      }
      else
      {
        debug_msg("20070702T1656Z: WARNING: Unable to extract \$before_path from \$dir!");
      }
      if(mkdir($dir,0777))
      {
        debug_msg("20080702T1522Z: mkdir(\"$dir\",0777) succeeded.");
        debug_msg("20080708T1700Z: chmod'ing \"$dir\" to 777...");
        if(chmod($dir,0777))
        {
          debug_msg("20080708T1703Z: chmod command was successful.");
          debug_msg("20080702T1525Z: returning TRUE from validate_dir(\"$dir\")...");
          return TRUE;
        }
        else
        {
          debug_msg("20080708T1702Z: chmod command was unsuccessful.");
        }
      }
      else
      {
        debug_msg("20080702T1521Z: unable to mkdir(\"$dir\",0777)!");
        ech("<font color=\"red\">ERROR.  Unable to create directory \"$dir\".</font><br>\n");
      }
    }
  }
}

function mysql_current_db()
{
  # borrowed this from http://us3.php.net/manual/en/function.mysql-db-name.php#51013
  if($r = @mysql_query("SELECT DATABASE()"))
  {
    return mysql_result($r,0);
  }
}

function add_textfile_contents_to_worklog($form_name,$recnum,$filename,$prefix_lines=FALSE)
{
  # This function will add the contents of the specified texfile ($filename)
  # to the worklog of the specified $form_name in Remedy, record number $recnum.
  #
  # This function will return FALSE upon success, or the Remedy error message
  # upon error.
  #
  # $prefix_lines is an optional array of lines that will be prefixed in the worklog update before the contents of $filename are added.
  # When using this, newlines are not added by this function, so if you want each prefix line to have a newline, add it to each array element value.
  #
  if(file_exists($filename))
  {
    if(is_readable($filename))
    {
      if($prefix_lines)
      {
        $temp_filename = tempnam('/tmp','deinstall_script');
        debug_msg("201007010141: \$temp_filename = \"$temp_filename\"");
        if($temp_fh = fopen($temp_filename,'w'))
        {
          foreach($prefix_lines as $ord => $prefix_line)
          {
            if(fwrite($temp_fh,$prefix_line)===FALSE)
            {
              return 'An error ocurred when fwriting to a temp file';
            }
          }
          $other_file_lines = file($filename);
          foreach($other_file_lines as $ord => $line)
          {
            if(fwrite($temp_fh,$line)===FALSE)
            {
              return 'An error ocurred when fwriting to a temp file';
            }
          }
          fclose($temp_fh);
          $filename = $temp_filename;
        }
        else
        {
          return "Unable to fopen(\"$temp_filename\",'w').";
        }
      }
      $cmd = "/home/gcooper/shared/scripts/php/deinstall/add_to_any_worklog.pl --form=\"$form_name\" --tkt=\"$recnum\" --file=\"$filename\"";
      debug_msg("201007010145: \$cmd= $cmd");
      if($output = run_command($cmd,1,1))
      {
        foreach($output as $ord => $line)
        {
          if(preg_match("/Updated worklog of (\S+) with the contents of/",$line))
          {
            debug_msg("201012031233: looks like the worklog was updated.  Returning...");
            return;
          }
          elseif($line)
          {
            return $line;
          }
        }
        return "An error occurred within the add_textfile_contents_to_worklog function";
      }
      else
      {
        return "Received no output from command: $cmd";
      }
    }
    else
    {
      return "File is not readable: \"$filename\"";
    }
  }
  else
  {
    return "File does not exist: \"$filename\"";
  }
}

function load_worklog_from_device_ticket($device_record_num)
{
  $cmd = "/home/gcooper/shared/scripts/php/deinstall/read_worklog.pl --form=DEVICE --tkt=$device_record_num";
  if($output = run_command($cmd,1,1))
  {
    $in_entry = FALSE;
    $header = array();
    $worklog = array();
    $lines = array();
    foreach($output as $ord => $line)
    {
      if($header)
      {
        if(preg_match("/^\-\-\-\-\-\-\-\-\-\-worklog\_entry\:/",$line))
        {
          $stuff = array();
          $stuff['header'] = $header;
          $stuff['lines'] = $lines;
          $worklog[] = $stuff;
          $header = array();
          $lines = array();
        }
        else
        {
          $lines[] = $line;
        }
      }
      elseif($in_entry)
      {
        # Thu Sep 21 12:22:11 2006        Eric Kahler
        if(preg_match("/^(Sun|Mon|Tue|Wed|Thu|Fri|Sat) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s+(\d+)\s+(\d+\:\d+\:\d+)\s+(\d+)\s+(.+)/",$line,$hit))
        {
          $header['dayofweek'] = $hit[1];
          $header['month'] = $hit[2];
          $header['day'] = $hit[3];
          $header['time'] = $hit[4];
          $header['year'] = $hit[5];
          $header['user'] = $hit[6];
        }
        else
        {
          $in_entry = FALSE;
        }
      }
      elseif(preg_match("/^\-\-\-\-\-\-\-\-\-\-worklog\_entry\:/",$line,$hit))
      {
        $in_entry = TRUE;
      }
    }
    $stuff = array();
    if($header)
    {
      $stuff['header'] = $header;
      $stuff['lines'] = $lines;
      $worklog[] = $stuff;
      $header = array();
      $lines = array();
    }
  }
  else
  {
    echocolor("ERROR: unable to read worklog of $device_record_num.\n",'light_red');
    script_abort('201007011336');
  }
  return $worklog;
}

function increment_counter($counter_id,$increment_by=FALSE)
{
  if(!($increment_by))
  {
    $increment_by = 1;
  }
  $cmd = "php -q /home/gcooper/shared/scripts/php/counter/counter.php --counter_id=\"$counter_id\" --increment_by=\"$increment_by\"";
  debug_msg("201007131125: \$cmd= $cmd");
  $output = run_command($cmd,1,1);
  debug_msg("201007121344: what does the \$output array look like?");
  debug_arr($output,'output');
}

function mins_of_day($hour,$min)
{
  # This function will return the number of minutes that have elapsed since midnight (00:00) for the specified $hour and $min.
  # For example; if the specified time is 00:01, the $mins returned will be 1.
  #            ; if the speicifed time is 01:00, the $mins returned will be 60.
  #            ; if the specified time is 16:30, the $mins returned will be ((16*60)+30) = 990.
  $mins = (($hour*60)+$min);
  debug_msg("201006171249: \$mins = \"$mins\"");
  return $mins;
}

function update_field($form,$recordNumber,$fieldID,$value,$exitOnFailure=FALSE)
{
  # This function will update a field's value in Remedy.
  # It will return TRUE if the field was successfully updated.
  # If the update is unsuccessful, you can pass a POINTCODE as the 5th arg
  # and this function will call script_abort($exitOnFailure).
 
  $update_field_script_filename = '/home/gcooper/shared/scripts/perl/update_field/update_field.pl';
  if(!(file_exists($update_field_script_filename)))
  {
    script_abort('201202241058');
  }

  $cmd = "$update_field_script_filename --form=\"$form\" --tkt=\"$recordNumber\" --fid=\"$fieldID\" --fv=\"$value\"";
  debug_msg("201103091317: \$cmd= $cmd");
  $output = run_command($cmd,1,1);
  debug_msg("201103091318: what does the \$output array look like?");
  debug_arr($output,'output');
  $updated = FALSE;
  foreach($output as $ord => $line)
  {
    if(preg_match("/Updated/",$line))
    {
      debug_msg("201103091322: field was updated successfully.");
      return TRUE;
    }
  }
  if($exitOnFailure)
  {
    debug_msg("201103091335: next line will call script_abort(\"$exitOnFailure\")...");
    if(function_exists('script_abort'))
    {
      script_abort("$exitOnFailure");
    }
    else
    {
      debug_msg("201103091339: next line will echo...");
      echo "ERROR: Unable to update field $fieldID in $form record number $recordNumber!\n";
      echo "Script aborted.\n";
      echo "POINTCODE: $exitOnFailure\n";
      exit;
    }
  }
}

function prettyPrint( $json )
{
  # borrowed this snippet from http://stackoverflow.com/questions/6054033/pretty-printing-json-with-php
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

if(!(function_exists('this_should_never_happen')))
{
  function this_should_never_happen($pointcode)
  {
    global $script_admin_name;
    $colors = define_colors();
    $red = $colors['red'];
    echo "<font color=\"$red\">ERROR.  This should never happen.<br>\n";
    echo "Please inform $script_admin_name about this problem.</font><br>\n";
    script_aborted($pointcode);
  }
}

function define_username()
{
  global $_SERVER;
  if(isset($_SERVER['LOGNAME']))
  {
    $username = $_SERVER['LOGNAME'];
  }
  else
  {
    $username = 'UNKNOWN';
  }
  debug_msg("201306251606: returning \"$username\" from define_username()...");
  return $username;
}

function standard_or_extended_access_list($acl,$rtr)
{
  debug_msg("201101131321: function standard_or_extended_access_list(\"$acl\",\"$rtr\") START:");
  $type = FALSE;
  if(preg_match("/^(\d+)$/",$acl))
  {
    if($acl>=100 && $acl<=199)
    {
      $type = 'extended';
    }
    elseif(($acl>=1 && $acl<=99)||($acl>=1300 && $acl<=1999))
    {
      $type = 'standard';
    }
  }
  else
  {
    if($cfg_lines = load_config($rtr))
    {
      $aclsearch = str_replace("-","\-",$acl);
      $aclsearch = str_replace("<","\<",$aclsearch);
      $aclsearch = str_replace(">","\>",$aclsearch);
      foreach($cfg_lines as $ord => $line)
      {
        if(preg_match("/^ip access\-list (standard|extended) $aclsearch$/",$line,$hit))
        {
          $type = $hit[1];
        }
      }
    }
  }
  if($type)
  {
    debug_msg("201101131333: \$type = \"$type\"");
    return $type;
  }
  else
  {
    this_should_never_happen('201101131334');
  }
}

function is_reachable($devicename)
{
  # To make this function quicker, we'll first try running just 1 ping;
  $cmd = "ping -c 1 $devicename";
  $output_lines = run_command($cmd,1,1);
  foreach($output_lines as $ord => $output_line)
  {
    if(preg_match("/time=/",$output_line,$hit))
    {
      return true;
    }
  }
  
  # If we're still within this function, it means that first attempt failed.
  # Perhaps there is packet loss.  Let's really be sure we're not getting any
  # response before calling it quits.
  $cmd = "ping -c 5 $devicename";
  $output_lines = run_command($cmd,1,1);
  foreach($output_lines as $ord => $output_line)
  {
    if(preg_match("/time=/",$output_line,$hit))
    {
      return true;
    }
  }
}

function funny_wait()
{
  # This function will say something funny, chosen at random, meant to be
  # echo'ed to the user while they're waiting for the script to perform
  # some mundane task that takes a while to complete.
  $msgs = array();
  $msgs[] = 'Engaging warp drive... ';
  $msgs[] = 'Fluffing pillows... ';
  $msgs[] = 'Checking oil level... ';
  $msgs[] = 'Counting sheep... ';
  $msgs[] = 'Optimizing user experience... ';
  $msgs[] = 'Checking for clues... ';
  $msgs[] = 'Flipping pancakes... ';
  $msgs[] = 'Pouring a drink... ';
  $msgs[] = 'Revving engines... ';
  $msgs[] = 'Surfing YouTube... ';
  $msgs[] = 'Preparing for battle... ';
  $msgs[] = 'Sharpening knives... ';
  $msgs[] = 'Obtaining unobtanium... ';
  $msgs[] = 'Licking my chops... ';
  $msgs[] = 'Chasing rainbows... ';
  $msgs[] = 'Entering trance-like state... ';
  $msgs[] = 'Taking out the trash... ';
  $msgs[] = 'Begging for handouts... ';
  $msgs[] = 'Doing something important... ';
  $msgs[] = 'Charging dead cellphone battery... ';
  $msgs[] = 'Making you wait... ';
  $msgs[] = 'Baking a cake... ';
  $msgs[] = 'Nuking some White Castle burgers... ';
  $msgs[] = 'Dreaming within a dream... ';
  $msgs[] = 'Checking flux capacitor... ';
  $msgs[] = 'Taking the red pill... ';
  $msgs[] = 'Kicking some @55... ';
  $msgs[] = 'Going through the wormhole... ';
  $howmany = count($msgs);
  debug_msg("201505042309: \$howmany = \"$howmany\"");
  shuffle($msgs);
  $chosen = $msgs[0];
  debug_msg("201505042317: \$chosen = \"$chosen\"");
  return $chosen;
}

?>
