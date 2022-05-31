<?php 
namespace LR\CountdownTimer\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public function countWeekendDays($start, $end)
    {
        // $start in timestamp
        // $end in timestamp
        $iter = 86400; // whole day in seconds
        $count = 0; // keep a count of Sats & Suns

        for($i = $start; $i <= $end; $i=$i+$iter)
        {
            if(Date('D',$i) == 'Sat' || Date('D',$i) == 'Sun')
            {
                $count++;
            }
        }
        return $count;
   }
}
?>