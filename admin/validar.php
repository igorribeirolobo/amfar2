<?
$pgto='20061010';
$hoje=date('Ymd');
$diff=$hoje-$pgto;
echo "pgto=$pgto hoje=$hoje diff=$diff<br>validade até ";
echo date('Y-m-d', time() + ((365-$diff) * 86400));
?>