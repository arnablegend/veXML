<?php
	$xml=null;
	$array=null;
	if (file_exists('Telemann.xml')) {
    	$xml = simplexml_load_file('Telemann.xml');
    	$json=json_encode($xml);
    	$array=json_decode($json,TRUE);
		//print_r($array);	
		
	} else {
    	exit('Failed to open xml file.');
	}
	foreach($array['part'] as $staff){
		$division=0;
		foreach($staff['measure'] as $measure){
			//print_r($measure);
			echo '<br>';
			if(isset($measure['@attributes']) && $measure['@attributes']['number']==1){
				$division=$measure['attributes']['divisions'];
				$clefs=$measure['attributes']['clef'];
				if(isset($clefs[0]['sign'])){
					foreach($clefs as $clef){
						echo $clef['sign'].' ';
					}
				}else{
					echo $clefs['sign'].' ';
				}
			}
			if(isset($measure['note'])){
				//print_r($measure['note']);
				//echo ' '.$measure['@attributes']['number'].' ';
				
				if(isset($measure['note'][0])){
					foreach($measure['note'] as $note){
						if(isset($note['rest'])){
							echo 'R '.$note['duration'].' ';							
						}
						if(isset($note['pitch'])){
							echo $note['pitch']['step'].' '.$note['pitch']['octave'].' '.$note['duration'].' ';
						}
					}	
				}
				if(isset($measure['note']['rest'])){
					echo 'R '.$measure['note']['duration'].' ';
				}
				if(isset($measure['note']['pitch'])){				
					echo $measure['note']['pitch']['step'].' '.$measure['note']['pitch']['octave'].' '.$measure['note']['duration'].' ';
				}
				echo $measure['@attributes']['number'].' ';
			}
		}
		echo '<br>';
	}
		
	//print_r($xml->part[0]->measure[0]->attributes->time);
	//echo $xml->part[0]->measure[0]->attributes->clef->sign;
	//echo $array['part'][0]['measure'][0]['attributes']['time']['beat-type'];
	//echo $array['part'][0]['measure'][0]['attributes']['clef']['sign'];
	
?>