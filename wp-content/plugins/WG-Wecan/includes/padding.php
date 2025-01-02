<?php

function beginpaging($pagesize=20,$recordcount,$paged){
	$startrow=0;
    
	$startrow = ($paged - 1) * $pagesize;
     
     if($paged % $pagesize == 0){
          $counterstart = $paged - ($pagesize - 1);
     }else{
          $counterstart = $paged - ($paged % $pagesize) + 1;
     }

     $counterend = $counterstart + ($pagesize - 1);
     
     $maxpage = $recordcount % $pagesize;

     if($recordcount % $pagesize == 0){
          $maxpage = $recordcount / $pagesize;
     }else{
          $maxpage = ceil($recordcount / $pagesize);
     }
     
	return array(  $startrow, $counterstart, $counterend, $maxpage );
}

function paddingpage($module, $counterstart,$counterend,$maxpage,$paged,$pagesize,$recordcount, $s=''){
	$page_url = 'admin.php?page='. $module.$s;
	$counterstart = $paged - 5;
	$counterend = $paged + 5;
	if($counterstart<1) $counterstart = 1;
	if($counterend > $maxpage) $counterend = $maxpage;
	
	if($counterstart != 1){
		$PrevStart = $counterstart - 1;
		$str .= "<a href=\"".$page_url."&paged=1\">«</a> ";
		$str .= "<a href=\"".$page_url."&paged=$PrevStart\">‹</a> ";
	}
	
	$c = 0;
	for($c=$counterstart;$c<=$counterend;$c++){
		if($c < $maxpage){
			if($c == $paged){
				if($c % $pagesize == 0){
				   $str .= "<a class='number current'>$c</a> ";
				}else{
				   $str .= "<a class='number current'>$c</a> ";
				} 
			}elseif($c % $pagesize == 0){
			   $str .= "<a class='number' href=\"".$page_url."&paged=$c\">$c</a> ";
			}else{
			   $str .= "<a class='number' href=\"".$page_url."&paged=$c\">$c</a> ";
			}
	  }else{
		   if($paged == $maxpage){
			   $str .= "<a class='number current'>$c</a> ";
			   break;
		   }else{
			   $str .= "<a class='number' href=\"".$page_url."&paged=$c\">$c</a> ";
			   break;
		   }
	   }
	}
	
	if($counterend < $maxpage){
		$NextPage = $counterend + 1;
		$str .= "<a href=\"".$page_url."&paged=$NextPage\">›</a> ";
	}
	
	if($counterend < $maxpage){
		$LastRec = $recordcount % $pagesize;
		if($LastRec == 0){
			$LastStartRecord = $recordcount - $pagesize;
		}else{
			$LastStartRecord = $recordcount - $LastRec;
		}
		$str .= "<a href=\"".$page_url."&paged=$maxpage\">»</a> ";
	}
	
	
	$str = '<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">'.$recordcount.' items</span>
			<span class="pagination-links">
					'.$str.'
				
			</span>
		</div>
		<br class="clear">
	</div>';
	
	return $str;
}

?>