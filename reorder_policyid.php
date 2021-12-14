<?php
 if(count($argv) !=2)
  exit("Usage: php change_order.php xxxxxx.cfg\n");
 $file = $argv[1];
 error_reporting(0);
 $arr = array();
 $pastearr = array();
 $tmp = array();
 exec("sed -n '/config firewall policy/,/^end/p' $file  | grep edit | awk '{print $2}' | sort -n",$arr);
 $count = count($arr);
 for($i=0;$i<$count;$i++)
 {
  if(($arr[$i+1]-$arr[$i]) != 1)
  {
   $sub = $arr[$i+1]-$arr[$i];
   for($j=1;$j<$sub;$j++)
   {
    $pastearr[] = $arr[$i]+$j;
   }
  }
 }
 $pastecount = count($pastearr);
 echo "config firewall policy\n";
 for($i=0;$i<$pastecount;$i++)
 {
  $from = $count-$i-1;
  $to = $pastearr[$i];
  unset($tmp);
  exec("sed -n '/config firewall policy/,/^end/p' $file | sed -n '/^    edit $from/,/^    next/p'",$tmp);
  $print = count($tmp);
  echo "    edit $to\n";
  for($j=0;$j<$print;$j++)
  {
   if(!preg_match("/uuid/",$tmp[$j]) and !preg_match("/^    edit/",$tmp[$j]))
    echo $tmp[$j]."\n";
  }
  echo "    delete $from\n";
 }
 echo "end\n";
?>
