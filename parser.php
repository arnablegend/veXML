<!DOCTYPE html>
<html lang="en">
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://unpkg.com/vexflow/releases/vexflow-debug.js"></script>
</head>
<body>

<div id="drawing"></div>
<script type="text/javascript">
	$(document).ready(function(){
		VF = Vex.Flow;		
		var div = document.getElementById("drawing");
		var renderer = new VF.Renderer(div, VF.Renderer.Backends.SVG);
		renderer.resize(1300,2000);

		var context = renderer.getContext();
		context.setFont("Arial", 10, "").setBackgroundFillStyle("#eed");
		var stave=null;
		var voice=null;
		var formatter=null;
		

		<?php
		$xml=null;
		$array=null;
	if (file_exists('Telemann.xml')) {
    	$xml = simplexml_load_file('Telemann.xml');
    	$json=json_encode($xml);
    	$array=json_decode($json,TRUE);
	} else {
    	exit('Failed to open xml file.');
	}
	$row=0;
	$column=0;
	foreach($array['part'] as $staff){
		$division=0;
		$upper=0;
		$lower=0;
		$staff_clef=null;
		foreach($staff['measure'] as $measure){
			//print_r($measure);
			if($column>=16){
				$column=0;
				$row++;
			}
			echo 'stave = new VF.Stave('.(10+75*$column).','.(20+100*$row) .', 400);
					';
			
			if(isset($measure['@attributes']) && $measure['@attributes']['number']==1){
				$division=$measure['attributes']['divisions'];
				$upper=$staff['measure'][0]['attributes']['time']['beats'];
				$lower=$staff['measure'][0]['attributes']['time']['beat-type'];
				$clefs=$measure['attributes']['clef'];
				if(isset($clefs[0]['sign'])){
					foreach($clefs as $clef){
						if($clef['sign']=='G'){
							echo '
						stave.addClef("treble").addTimeSignature("'.$upper.'/'.$lower.'");
						';
						$staff_clef='G';
						}else if($clef['sign']=='F'){
							echo '
								stave.addClef("bass").addTimeSignature("'.$upper.'/'.$lower.'");
								';
							$staff_clef='F';
						}
					}
				}else{
					if($clefs['sign']=='G'){
							echo '
						stave.addClef("treble").addTimeSignature("'.$upper.'/'.$lower.'");
						';
						$staff_clef='G';
						}else if($clefs['sign']=='F'){
							echo '
								stave.addClef("bass").addTimeSignature("'.$upper.'/'.$lower.'");
								';
							$staff_clef='F';
						}
				}
				
			}
			if(isset($measure['note'])){
				echo 'var notes=[';
				if(isset($measure['note'][0])){
					foreach($measure['note'] as $note){
						if(isset($note['rest'])){
							$duration=$note['duration'];
							switch($division/$duration){
								case 1:
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["b/'.$lower.'"], duration: "qr" }),';
									break;
								case 2:
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["b/'.$lower.'"], duration: "8r" }),';
									break;
								case 3:
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["b/'.$lower.'"], duration: "16r" }),';
									break;
								case 4:
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["b/'.$lower.'"], duration: "32r" }),';
									break;
								default:
									echo 'not okay rest'; 
							}							
						}
						if(isset($note['pitch'])){
							$duration=$note['duration'];
							switch($note['type']){
								case 'quarter':
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["'.strtolower($note['pitch']['step']).'/'.$lower.'"], duration: "q" })';
									break;
								case 'eighth':
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["'.strtolower($note['pitch']['step']).'/'.$lower.'"], duration: "8d" })';
									break;
								case '16th':
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["'.strtolower($note['pitch']['step']).'/'.$lower.'"], duration: "16" })';
									break;
								case '32nd':
									echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["'.strtolower($note['pitch']['step']).'/'.$lower.'"], duration: "32" })';
									break;
								default:
									echo 'not okay';
										 
							}
							if(isset($note['dot'])){
								echo '.addDot(0),';
							}else{
								echo ',';
							}
						}
					}	
				}
				if(isset($measure['note']['rest'])){
					echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["b/'.$lower.'"], duration: "wr" }),';
				}
				if(isset($measure['note']['pitch'])){	
					echo 'new VF.StaveNote({clef:"'.(($staff_clef=='G')?'treble':'bass').'", keys:["'.strtolower($note['pitch']['step']).'/'.$lower.'"], duration: "w" })';			
					if(isset($note['dot'])){
								echo '.addDot(0),';
							}else{
								echo ',';
							}
				}
				echo 'null];';
				echo 'voice = new VF.Voice({num_beats: '.$upper.',  beat_value: '.$lower.'});
						voice.addTickables(notes);';
				echo 'formatter = new VF.Formatter().joinVoices([voice]).format([voice], 75);';
			}
			echo 'voice.draw(context, stave);';
			echo 'stave.setContext(context).draw();
			';
			$column++;
		}
		break;
	}
	
?>
		//obj.Stave(x,y,width)

		
	});

</script>
</body>
</html>