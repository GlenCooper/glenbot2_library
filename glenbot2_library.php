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
# 20181228T0534Z: trying to do anything useful
# 20190108T0438Z: Been busy cleaning up... my act, my attitude, the apartment I moved into, and a couple ancient POINTCODEs
# 20190108T0506Z: built a new subdomain & webpage recently... since it was a historic moment in my life: https://lastdrink.glencooper.com/
# 20190108T0928Z: converted a lot of the aliases over to a more easily updatable bash script, currently named "/home/mreth/bin/mylib", an executable bash shell script
# 20190108T1032Z: renamed /home/mreth/bin/mylib to /home/mreth/bin/mylib.sh, and made a symlink so simply typing "mylib" will still launch mylib.sh...
# 20190118T0634Z: met bitcoin firstbits 1MitchK in real life
# 20190204T0313Z: I'm still alive...
# 20190215T0949Z: something smells fishy with blockstack browser Linux install... my CPU is SCREAMING ever since (a couple hours ago)
# 20190303T0634Z: things calmed down with the CPU.  Blockstack isn't entirely fishy... but not worth my time now.  Maybe someday.  Sticking with keybase.glencooper.com for now
# 20190303T1547Z: making push.sh work a little harder!
# 20190311T0341Z: thinking of trying to use MySQL again...
# 20190313T1003Z: About to touch a MySQL database again, after watching Tone Vays live on YouTube (from Live from Token2049 in Hong Kong).  I hope I get to meet Tone some day.
# 20190323T0410Z: I built another subdomain recently; https://lnd.glencooper.com
# 20190401T1745Z: built another subdomain, and added a tawk.to chatbox to it: https://truth.glencooper.com/
# 20190404T0037Z: added tawkto function
# 20190413T1711Z: dealing with a lot of shitty health issues lately, but still pushing onward
# 20190416T1633Z: borrowing time_elapsed_string function from https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
# 20190426T0932Z: struggling with ongoing health issues
# 20190918T0524Z: constant health problems, too many to list here.  Primarily frequent migraines & vertigo, and relationship problems with Natalie
# 20200112T1110Z: wrote qrencode function
# 20200122T0420Z: trying to do anything productive, not having much luck yet...
# 20201218T144805Z: my previous laptop that I was running this from, 1satoshi, has died.  Total ssd sudden failure; won't boot.  New laptop "mab" has been born.
# 20210106T105753Z: Slowly recovering from a brutal hardware failure.  Migrated to mab
# 20210108T085014Z: migraines suck! I should look into investing in blackout blinds
# 20210108T140427Z: I think I have "mylib" alias set up properly now to interact with github via ssh
# 20210121T002104Z: why is this happening?  "PHP Fatal error:  Uncaught Error: Call to undefined function mysqli_connect() in /home/crystamped/glenbot2_library/glenbot2_library.php:1540"
# 20210130T042002Z: I really need to get this going again.  Right now I'm living on a prayer that mab doesn't crap out.
# 20211218T042636Z: changed "taking a red pill" to "taking an orange pill"

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
      debug_msg("20060301T0038Z: is_array(\"$var\") is TRUE");
      debug_msg("20060301T0041Z: NOTE: This part still needs to be written!!!");
      return $arr;
      #$new_arr["$var"] = array();
    }
    else
    {
      debug_msg("20060301T0039Z: is_array(\"$var\") is FALSE",1000);
      $new_arr["$var"] = htmlspecialchars($val);
    }
  }
  return $new_arr;
}

if(!(function_exists('qrencode')))
{
  debug_msg("20200112T11118Z: looks like we can create a new fuction called qrencode here.");
  function qrencode($target)
  {
    // create curl resource
    $ch = curl_init();

    // this is a quick and dirty way of doing things.  this will fail if the destination website is inaccessible.  this should be tightened up at some point...
    $url = 'qrenco.de/'.urlencode($target);
    curl_setopt($ch, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $output;
  }
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
  debug_msg("20160229T2237Z: function is_valid_net_ipv4_parse_address(\"$cidr\") START:");
  require_once('Net/IPv4.php');   # https://pear.php.net/manual/en/package.networking.net-ipv4.php
  $net = Net_IPv4::parseAddress($cidr);   # https://pear.php.net/manual/en/package.networking.net-ipv4.parseaddress.php
  if(PEAR::isError($net))    # https://pear.php.net/manual/en/core.pear.pear.iserror.php
  {
    debug_msg("20160229T2219Z: PEAR::isError(\$net) is TRUE!");
  }
  else
  {
    debug_msg("20160229T2222Z: converting \$net object to associative array");
    $stuff = (array) $net;
    debug_msg("20160229T2231Z: what does the \$stuff array look like?");
    debug_arr($stuff,'stuff');
    return $stuff;
  }
}

function is_valid_ip_v2($ip=FALSE,$which_value='',$silent_mode=FALSE)
{
  # This function was written to replace the original "is_valid_ip" function which used global variables.
  # This function was modified 20120803T1344Z such that if an IP like "10.27.30.07" is passed to it, it
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
      echocolor("POINTCODE: 20181008T1008Z\n",'light_red');
    }
    else
    {
      ech("<font color=\"$red\">FATAL ERROR!<br>Script aborted!</font><br>\n");
      ech("<font color=\"$red\">POINTCODE: 20120206T1609Z</font>\n");
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
      debug_msg("20050505T2214Z: count(\$octets) != 4.  Returning false.");
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
        debug_msg("20080722T1340Z: is_command_line_version() is TRUE",1000);
        if(isset($arrname))
        {
          debug_msg("20080722T1341Z: \$arrname is TRUE (\$arrname = \"$arrname\")",1000);
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
          debug_msg("20060301T0030Z: next line will call convert_all_arr_elements_to_html_special(\$arr)...",1000);
          $html_special_arr = convert_all_arr_elements_to_html_special($arr);
          debug_msg("20060301T0031Z: done with convert_all_arr_elements_to_html_special(\$arr).",1000);
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

function current_pointcode($raw=FALSE)
{
  # Format of POINTCODES is:
  # YYYYMMDDTHHMMSSZ
  # See https://timestamps.glencooper.com/ for explanation of syntax
  date_default_timezone_set('UTC');
  $pointcode_pt1 = date('Ymd');
  $pointcode_pt2 = date('His');
  if($raw)
  {
    debug_msg("20181215T0540Z: \$raw is TRUE, so not adding the textual separators");
    $pointcode = $pointcode_pt1 . $pointcode_pt2;
  }
  else
  {
    $pointcode = $pointcode_pt1 . 'T' . $pointcode_pt2 . 'Z';
  }
  debug_msg("20181206T032004Z: \$pointcode = \"$pointcode\"");
  return $pointcode;
}

function tripwire($pointcode=FALSE)
{
  if(!$pointcode)
  {
    $pointcode = current_pointcode();
    debug_msg("20191218T0531Z: \$pointcode = \"$pointcode\"");
  }
  # probably should have something here to send a push notification
  script_abort($pointcode);
}

function script_aborted($pointcode)
{
  global $colors;
  debug_msg("20060528T0321Z: function script_aborted(\"$pointcode\") START");
  debug_msg("20060528T0322Z: next line will call tripwire(\"$pointcode\")...");
  tripwire("$pointcode");
  debug_msg("20060528T0323Z: done with tripwire(\"$pointcode\").");
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
  $debug_msg = "20051020T2000Z: function list_of_files_in_directory(";
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
        debug_msg("20100319T1203Z: what does the \$exclude_patterns array look like?");
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
        debug_msg("20150327T1650Z: what does the \$match_on_patterns array look like?");
        debug_arr($match_on_patterns,'match_on_patterns');
      }
    }
  }
  $debug_msg.=") START";
  debug_msg($debug_msg,100);
  debug_msg("20060208T1419Z: \$dir = \"$dir\"");
  
  # make sure the $dir ends with a '/'.  If not, add it.
  $lastchar = $dir[strlen($dir)-1];
  if(($lastchar==='/')||($lastchar==="\\"))
  {
    debug_msg("20060208T1418Z: \$dir ends with a slash.");
  }
  else
  {
    debug_msg("20070531T1905Z: \$dir did not end with a slash, so one has been added.");
    $dir = $dir.'/';
    debug_msg("20060208T1421Z: \$dir is now \"$dir\".");
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
          debug_msg("20150327T1729Z: \$pattern= $pattern");
          if(preg_match($pattern,$file))
          {
            debug_msg("20150327T1730Z: matched pattern to file \"$file\"");
          }
          else
          {
            debug_msg("20150327T1731Z: pattern did not match on file \"$file\"");
            continue(2);  # skip the rest of the current outer loop iteration
          }
        }
      }
      $files[] = $file;
    }
    closedir($handle);
    if($match_on_patterns)
    {
      debug_msg("20150327T1721Z: \$match_on_patterns is TRUE");
      $timestamped = array();
      foreach($files as $filename)
      {
        $full_filename = $dir.$filename;
        $timestamped[$filename] = filemtime($full_filename);
      }
      arsort($timestamped);  # sort array in reverse order and maintain index association
      debug_msg("20150327T1715Z: what does the \$timestamped array look like?");
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
      debug_msg("20070426T1910Z: \$count_of_files = \"$count_of_files\"");
      return $count_of_files;
    }
    else
    {
      debug_msg("20051020T2147Z: what does the \$files array look like?",500);
      debug_arr($files,'files',500);
      debug_msg("20051020T2204Z: returning \$files from list_of_files_in_directory().",1000);
      return $files;
    }
  }
}

function run_command($cmd,$split_into_lines=FALSE,$silent_mode=FALSE,$remove_blank_lines=FALSE)
{
  # this function runs a command ($cmd).
  global $colors;
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
  debug_msg("20100413T1134Z: \$today = \"$today\"");
  $exclude_patterns = array();
  $exclude_patterns[] = "/^$today/";
  for($i=1;$i<$days_to_keep;$i++)
  {
    $minus_days = "-$i day";
    $numerical_date = date("Ymd",strtotime("$minus_days"));
    $exclude_patterns[] = "/^$numerical_date/";
  }
  debug_msg("20110822T1353Z: what does the \$exclude_patterns array look like?");
  debug_arr($exclude_patterns,'exclude_patterns');
  $list = list_of_files_in_directory($path,1,NULL,0,1,1,0,0,$exclude_patterns);
  debug_msg("20100413T1120Z: what does the \$list array look like?");
  debug_arr($list,'list');
  $all_old_logs_purged_successfully = TRUE;
  foreach($list as $filename)
  {
    # NOTE: Do not attempt to use echocolor within this function; it would cause a recursive call to this function
    #echocolor("Deleting old log file \"$filename\"... ",'light_blue');
    debug_msg("\n20100413T1229Z: next line is unlink(\"$filename\")...");
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
  debug_msg("20100413T1234Z: done with foreach loop");
  if($all_old_logs_purged_successfully)
  {
    debug_msg("20100413T1227Z: \$all_old_logs_purged_successfully is TRUE");
    return TRUE;
  }
  else
  {
    debug_msg("20100413T1228Z: \$all_old_logs_purged_successfully is FALSE");
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
  debug_msg("20070823T0058Z: \$text_left_of_select_button = \"$text_left_of_select_button\"",1000);
  if($num_args>4)
  {
    $word_wrap = func_get_arg(4);
    debug_msg("20071020T1500Z: \$word_wrap = \"$word_wrap\"");
  }
  else
  {
    $word_wrap = FALSE;
  }
  debug_msg("20051021T1412Z: function select_all_and_copy(\"$txt\",\"$name\") START",500);
  debug_msg("20051024T1526Z: need to get rid of .'s in the \$name (\"$name\").",1000);
  $formname = str_replace('.','_',$name);
  debug_msg("20051024T1527Z: \$formname = \"$formname\"",1000);
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
  debug_msg("20051104T1529Z: \$txt_count = \"$txt_count\"",1000);
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
      debug_msg("20071020T1501Z: is_array(\$txt) is TRUE");
      debug_msg("20071020T1507Z: what does the \$txt array look like?");
      debug_arr($txt,'txt');
      foreach($txt as $ord => $line)
      {
        $length_of_line = strlen($line);
        debug_msg("20071020T1503Z: \$length_of_line = \"$length_of_line\"",1000);
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
      debug_msg("20071020T1501Z: is_array(\$txt) is TRUE");
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
  debug_msg("20051021T1413Z: function select_all_and_copy(\"$txt\",\"$name\") END.",500);
}

function rebuild_options_without($omit)
{
  # this function manipulates the global variable $options string.  It returns a new $options string, sans and variables passed to it as arguments
  $num_args = func_num_args();
  for($i=0;$i<$num_args;$i++)
  {
    $passed_args[$i] = func_get_arg($i);
  }
  debug_msg("20051222T2123Z: function rebuild_options() START");
  if(!($passed_args))
  {
    return;
  }
  debug_msg("20051222T2115Z: next line will call explode_options()...");
  $options_arr = explode_options();
  debug_msg("20051222T2116Z: done with explode_options().");
  debug_msg("20051222T2120Z: what does the \$options_arr look like?");
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

  debug_msg("20190303T162329Z: function get_options START",1000);
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
  debug_msg("20190303T162346Z: \$options = \"$options\"",1000);
  debug_msg("20190303T162422Z: function get_options END",500);
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
    echo "POINTCODE: 20051118T0132Z\n";
    exit;
  }
  foreach($table as $filename => $timestamp)
  {
    # ugly way of accessing just the first in the list...
    return $filename;
  }
}

function ip2bin($ip)
{
  # Converts an IP Address to binary representation.
  # by Glen Cooper, GlenCooper.com
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
    #debug_msg("20100713T1552Z: \$final_answer = \"$final_answer\"");
    return $final_answer;
  }
}

function error_message($msg,$abort_with_pointcode=FALSE)
{
  global $colors;
  $red = $colors['red'];
  debug_msg("2013083T01203Z: function error_message(\"$msg\",\"$abort_with_pointcode\" START:");
  debug_msg("2013083T01213Z: next line is if(is_command_line_version())...");
  if(is_command_line_version())
  {
    debug_msg("20130830T1214Z: is_command_line_version() is TRUE");
    echocolor("$msg\n",'red');
    if($abort_with_pointcode)
    {
      script_abort($abort_with_pointcode);
    }
  }
  else
  {
    debug_msg("20130830T1214Z: is_command_line_version() is FALSE");
    ech("<font color=\"$red\">$msg</font><br>\n");
    if($abort_with_pointcode)
    {
      debug_msg("20130830T1145Z: \$abort_with_pointcode is TRUE");
      debug_msg("20130830T1146Z: \$abort_with_pointcode = \"$abort_with_pointcode\"");
      debug_msg("201308301200: next line will call script_abort(\"$abort_with_pointcode\")...");
      script_abort($abort_with_pointcode);
    }
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
  debug_msg("20060208T1128Z: function save_local_copy_of_library_to(\"\$localfile\") START");
  debug_msg("20060208T1133Z: \$localfile = \"$localfile\"");

  if(file_exists($localfile))
  {
    debug_msg("20060208T1132Z: \$localfile exists.");
    if(!(is_writable($localfile)))
    {
      debug_msg("20060208T1119Z: \$localfile is not writable!");
      return FALSE;
    }
  }
  else
  {
    debug_msg("20060208T1133Z: \$localfile does not exist.");
    $library_path = dirname($localfile);
    debug_msg("20140602T1442Z: \$library_path = \"$library_path\"");
    validate_path($library_path);
  }
  if(!$lines)
  {
    $lines = file("http://www.glencooper.com/php/gcooper_library.txt");
  }
  if(@ !$handle = fopen($localfile,'w'))
  {
    debug_msg("20060208T1120Z: unable to open \$localfile for writing!");
    return FALSE;
  }
  if(is_array($lines))
  {
    foreach($lines as $ord => $line)
    {
      if(fwrite($handle,$line) === FALSE)
      {
        debug_msg("20060208T1121Z: error writing data to \$localfile!");
        fclose($handle);
        return FALSE;
      }
    }
  }
  fclose($handle);
  debug_msg("20060208T1122Z: successfully saved library to \$localfile.");
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
  #
  # 20190325T0414Z: I'm just scanning thru this old code, looking for anything that can be blown away.  This function seem suspect.  Seems like there should be
  # a more efficient way than this.  I just don't know what it is.  Well anyway, at this point, this whole library is beginning to become questionable.
  $parts = preg_split("/\//",$pathname);
  $parts_count = count($parts);
  debug_msg("20060208T1515Z: \$parts_count = $parts_count",1000);
  $filename = $parts[$parts_count-1];
  debug_msg("20060208T1519Z: \$filename = \"$filename\"",1000);
  return $filename;
}

function connect_to_mysql_database($mysqlstuff=FALSE)
{
  debug_msg("20190313T1347Z: function connect_to_mysql_database(\"\$mysqlstuff\") START:",1000);
  if(!($mysqlstuff))
  {
    echo "<font color=\"red\">FATAL ERROR: \$mysqlstuff is FALSE!</font><br>\n";
    echo "<font color=\"red\">POINTCODE: 20190313T1348Z</font><br><br>\n";
    die();
  }
  debug_msg("20190313T1349Z: attempting mysqli_connect() using \$mysqlstuff login credentials...",1000);
  $hush = FALSE;
  if($hush)
  {
    $mysqli = @mysqli_connect($mysqlstuff['host'],$mysqlstuff['user'],$mysqlstuff['pass'],$mysqlstuff['database']);  # don't overlook the @!
    if($mysqli)
    {
      debug_msg("20190313T1350Z: \$mysqli is TRUE",1000);
      debug_msg("20190313T1351Z: returning \$mysqli from connect_to_mysql_database()",1000);
      return($db);
    }
    else
    {
      debug_msg("20190313T1352Z: \$mysqli is FALSE",1000);
      return false;
    }
  }
  else
  {
    debug_msg("20210130T041844Z: next line is mysqli_connect(".$mysqlstuff['host'].",".$mysqlstuff['user'].",".$mysqlstuff['pass'].",".$mysqlstuff['database'].")...");
    $mysqli = mysqli_connect($mysqlstuff['host'],$mysqlstuff['user'],$mysqlstuff['pass'],$mysqlstuff['database']);
    if($mysqli)
    {
      debug_msg("20190313T1236Z: \$mysqli is TRUE",1000);
      debug_msg("20190313T1237Z: returning \$mysqli from connect_to_mysql_database()",1000);
      return($mysqli);
    }
    else
    {
      debug_msg("20190313T1238Z: \$mysqli is FALSE",1000);
      return false;
    }
  }
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

function record_glenbot2_alive($task_name='missingtaskname',$script_name='missingscriptname')
{
  debug_msg("20190414T0323Z: function record_glenbot2_alive(\"$task_name\",\"$script_name\") START:\n");
  if(isset($_SERVER['SERVER_NAME']))
  {
    $servername = $_SERVER['SERVER_NAME'];
  }
  else
  {
    $servername = 'some CLI host?';
  }
  $db = talk_to_db();
  $alive_table_name = 'alive';
  $sql = "SELECT * FROM `$alive_table_name` WHERE `taskname` = \"$task_name\" AND `byscriptname` = \"$script_name\"";
  debug_msg("20190414T0357Z: \$sql = $sql;");
  $result = mysqli_query($db,$sql);
  $num_rows = FALSE;
  if($result)
  {
    debug_msg("20190413T191610Z: \$result is TRUE");
    $num_rows = mysqli_num_rows($result);
  }
  else
  {
    debug_msg("20190413T191657Z: \$result is FALSE");
  }
  debug_msg("20190413T191828Z: \$num_rows = \"$num_rows\"");
  $now = gmdate('Y-m-d H:i:s');
  debug_msg("20190414T051909Z: \$now = \"$now\"");
  if($num_rows)
  {
    debug_msg("20190414T035814Z: \$num_rows is TRUE");
    $sql = "UPDATE $alive_table_name SET `lastalive`=\"$now\" WHERE `taskname`=\"$task_name\" AND `byscriptname`=\"$script_name\"";
  }
  else
  {
    debug_msg("20190414T035814Z: \$num_rows is FALSE");
    $sql = "INSERT INTO `alive` (`id`, `taskname`, `lastalive`, `byscriptname`) VALUES (NULL, '$task_name', '$now', '$script_name')";
  }
  debug_msg("20190413T1930Z: \$sql= $sql;");
  unset($result);
  debug_msg("20190414T035918Z: next line is \$result = mysqli_query(\$db,\$sql)...");
  $result = mysqli_query($db,$sql);
  if($result)
  {
    debug_msg("20190413T193125Z: \$result is TRUE (\$result = \"$result\")");
    return TRUE;
  }
  else
  {
    debug_msg("20190413T193159Z: \$result is FALSE");
    return false;
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

function convert_options_to_hidden_form_values($options)
{
  # this function will convert $options, the URL options passed at runtime, to hidden form values meant to
  # be passed back to the script via a html form using method = POST.  The script will return an array of
  # <input type=hidden> strings so they can be echo'ed within a <form method=POST>.
  debug_msg("20060130T1910Z: function add_hidden_post_form_values(\"\$options\") START");
  debug_msg("20060130T1909Z: \$options = \"$options\"");
  if(!($options))
  {
    return false;
  }
  $option_pairs = explode('&',$options);
  $i = 0;
  $option_pairs_count = count($option_pairs);
  debug_msg("20060130T1915Z: \$option_pairs_count = \"$option_pairs_count\"");
  debug_msg("20060130T1918Z: what does \$option_pairs look like?");
  debug_arr($option_pairs,'option_pairs');
  while($i<$option_pairs_count)
  {
    $current_pair = $option_pairs[$i];
    debug_msg("20060130T1920Z: \$current_pair = \"$current_pair\"");
    $option_pair = split('=',$current_pair);
    $string = "<input type=\"hidden\" name=\"".$option_pair[0]."\" value=\"".$option_pair[1]."\">\n";
    $output_lines[] = $string;
    $i++;
  }
  $output_lines_count = count($output_lines);
  debug_msg("20060130T1919Z: \$output_lines_count = \"$output_lines_count\"");
  debug_msg("20060130T1914Z: what does \$output_lines look like?");
  debug_arr($output_lines,'output_lines',NULL,1);
  return $output_lines;
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

function mins_of_day($hour,$min)
{
  # This function will return the number of minutes that have elapsed since midnight (00:00) for the specified $hour and $min.
  # For example; if the specified time is 00:01, the $mins returned will be 1.
  #            ; if the speicifed time is 01:00, the $mins returned will be 60.
  #            ; if the specified time is 16:30, the $mins returned will be ((16*60)+30) = 990.
  $mins = (($hour*60)+$min);
  debug_msg("20100617T1249Z: \$mins = \"$mins\"");
  return $mins;
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
  debug_msg("20130625T1606Z: returning \"$username\" from define_username()...");
  return $username;
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
  $msgs[] = 'Taking an orange pill... ';
  $msgs[] = 'Kicking some @55... ';
  $msgs[] = 'Going through the wormhole... ';
  shuffle($msgs);
  $chosen = $msgs[0];
  debug_msg("20150504T2317Z: \$chosen = \"$chosen\"");
  return $chosen;
}

function time_elapsed_string($datetime, $full = false)
{
  $now = new DateTime;
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);

  $diff->w = floor($diff->d / 7);
  $diff->d -= $diff->w * 7;

  $string = array('y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second',);
  foreach($string as $k => &$v)
  {
    if($diff->$k)
    {
       $v = $diff->$k.' '.$v.($diff->$k > 1 ? 's' : '');
    }
    else
    {
      unset($string[$k]);
    }
  }

  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function tawkto()
{
  // This function quickly adds a chatbox provided by https://www.tawk.to
  // When adding a new "property" within their Admin interface, they provide a snippet of code that should be inserted on the page you want the chatbox on.
  // A quick way to do it is to save that code snippet into a file called "tawk.txt" and place that file in the same directory as the php file that calls this function.
  // This function will likely be rewritten/enhanced in the future to utilize a database of some sort.  For now, this is better than adding their code to my pages.
  $tawk_filename = 'tawk.txt';
  if(file_exists($tawk_filename))
  {
    debug_msg("20190404T0745Z: file_exists($tawk_filename) is TRUE");
    $tawk_lines = file('tawk.txt');
    foreach($tawk_lines as $tawk_line)
    {
      echo "$tawk_line\n";
    }
  }
  else
  {
    debug_msg("20190404T0746Z: file_exists($tawk_filename) is FALSE");
  }
}

function talk_to_db()
{
  debug_msg("20190313T1002Z: hope I get to meet Tone Vays someday.");
  $mysqlstuff = secret_db_stuff();
  if($db = connect_to_mysql_database($mysqlstuff))
  {
  	debug_msg("20190313T1226Z: \$db is TRUE (so we were able to connect to the database).  Returning \$db...");
	return $db;
  }
  else
  {
    debug_msg("20190313T1226Z: \$db is FALSE");
  }
}

?>
