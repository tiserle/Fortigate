<?php
 error_reporting(0);
 if(count($argv) !=2)
  exit("Usage: php reorder_policyid.php xxxxxx.cfg\n");
 $file = $argv[1];
 $arr = array();
 $pastearr = array();
 $tmp = array();
 exec("sed -n '/config firewall policy/,/^end/p' $file  | egrep \"^    edit\" | awk '{print $2}' | sort -n",$arr);
 //print_r($arr);
 // 計算需要補幾行
 $count = count($arr);
 $need = 1;
 for($i=0;$i<$count;$i++)
 {
  if(($arr[$i+1]-$arr[$i]) != 1)
  {
   $need++;
   break;
  }
 }
 $needcount = $count - $need;
 // 取出要補的行號
 for($i=0;$i<$count;$i++)
 {
  if(($arr[$i+1]-$arr[$i]) != 1)
  {
   $k++;
   $sub = $arr[$i+1]-$arr[$i];
   for($j=1;$j<$sub;$j++)
   {
    if(count($pastearr) == $needcount)
     break;
    else
     $pastearr[] = $arr[$i]+$j;
   }
  }
 }
 //print_r($pastearr);
 echo "config firewall policy\n";
 for($i=0;$i<$needcount;$i++)
 {
  /*
  if($i==0)
   $from = end($arr);
  elseif($i>0)
   $from = prev($arr);
  */
  $to = $pastearr[$i];
  $from = $arr[$count-$i-1];
  unset($tmp);
  exec("sed -n '/^config firewall policy$/,/^end$/p' $file | sed -n '/^    edit $from/,/^    next/p'",$tmp);
  $print = count($tmp);
  echo "    edit $to\n";
  for($j=0;$j<$print;$j++)
  {
   if(!preg_match("/uuid/",$tmp[$j]) and !preg_match("/^    edit/",$tmp[$j]))
    echo $tmp[$j]."\n";
  }
  echo "    move $to before $from\n";
  echo "    delete $from\n";
 }
 echo "end\n";
?>
