<h4>Run Your Code!</h4>
<hr />

<?=validation_errors();?>

<form method="post" class="form-horizontal" id="custom_run">
	<div class="well textarea span7" style="padding:0">
		<textarea id="texteditor" name="texteditor" class="span12" rows="22"><?=$code?></textarea>
 		<div>
 			<script src="application/third_party/codemirror/lib/codemirror.js" charset="utf-8"></script>
			<link rel="stylesheet" href="application/third_party/codemirror/lib/codemirror.css" />
 			<link rel="stylesheet" href="application/third_party/codemirror/theme/neat.css" />
 			<script src="application/third_party/codemirror/mode/clike/clike.js" charset="utf-8"></script>
 			<script src="application/third_party/codemirror/mode/pascal/pascal.js" charset="utf-8"></script>
			<script src="application/third_party/codemirror/mode/python/python.js" charset="utf-8"></script>
			<script type="text/javascript">
				var editor = CodeMirror.fromTextArea($("#texteditor").get(0), {
 						mode: "<?php if ($language=='Pascal') echo "text/x-pascal"; else echo "text/x-c++src";?>",
 						lineNumbers: true,
						theme: "neat",
 						indentUnit: 4,
 						smartIndent: true,
 						tabSize: 4,
 						indentWithTabs: true,
 						autofocus: true
					}
				);
			</script>
		</div> 
		<input type="checkbox" value="codemirror" id="toggle_editor" name="toggle_editor" checked style="margin-left: 28px"/>
		<label for="editor" style="display:inline">Toggle CodeMirror</label>
	</div>
	
	<div class="span4" style="margin-left:48px">
		<div class="run_control">
			<label for="language">Language
				<select name="language" id="language" class="language" onchange="language_on_change();">
					<option value="C"<?php if ($language=="C") echo ' selected';?> >C</option>
					<option value="C++"<?php if ($language=="C++") echo ' selected';?> >C++</option>
					<option value="C++11"<?php if ($language=="C++11") echo ' selected';?> >C++11(0x)</option>
					<option value="Pascal"<?php if ($language=="Pascal") echo ' selected';?> >Pascal</option>
					<option value="Java"<?php if ($language=="Java") echo ' selected';?> >Java</option>
					<option value="Python"<?php if ($language=="Python") echo ' selected';?> >Python</option>
				</select>
			</label>
			
			<label for="input">Input </label>
			<textarea id="input" name="input" class="span12" rows="8"><?=$input?></textarea>
			
			<label for="input">Output</label>
			<textarea id="output" class="span12" rows="8" readonly><?=$output?></textarea>
			
			<?php if (isset($time) && $time !== false) { ?>
			<label>Time
				<span class="label label-info"><?=$time?> ms</span>
			</label>
			<?php } 
			
				if (isset($memory) && $memory !== false) { ?>
			<label>Memory
				<span class="label label-info"><?=$memory?> KB</span>
			</label>
			<?php } ?>
		</div>
		
	</div>
	<button type="submit" class="btn btn-primary pull-right" id="btn_run">Run</button>
</form>
	
	
<script type="text/javascript">
	$(document).ready(function(){
		$('#toggle_editor').change(function (){
			if ($('#toggle_editor').attr("checked")){
				$('.CodeMirror').css({"visibility" : "visible", "display" : "block"});
				$('#texteditor').css({"visibility" : "hidden", "display" : "none", "zIndex" : -10000});
				editor.setValue($('#texteditor').val());
			}else{
				$('.CodeMirror').css({"visibility" : "hidden", "display" : "none"});
				$('#texteditor').css({"visibility" : "visible", "display" : "block", "zIndex" : 10000});
				editor.save();
			}
		}),
			
		$("#btn_run").click(function() {
			if ($('#toggle_editor').attr("checked")) editor.save();
			if ($('#texteditor').val().length == 0){
				alert("You are attempting to run empty code!");
				return false;
			}
			if (($('#language').val() in {'C':1, 'C++':1}) && $('#texteditor').val().indexOf('%I64d') != -1){
				alert("Please be reminded to use '%lld' specificator in C/C++ instead of '%I64d'!");
				return false;
			}
			$('#custom_run').ajaxSubmit({
				url: 'index.php/customtest/run',
				success: function(responseText){
					$('#page_content').html(responseText);
				}
			});
			return false;
		})
	})
	$(".CodeMirror-linenumbers").width(28);

	function language_on_change(){
		var language = $('#language').val();
		if (language == "C") editor.setOption("mode", "text/x-csrc");
		if (language == "C++" || language == "C++11") editor.setOption("mode", "text/x-c++src");
		if (language == "Pascal") editor.setOption("mode", "text/x-pascal");
		if (language == "Java") editor.setOption("mode", "text/x-java");
		if (language == "Python") editor.setOption("mode", "text/x-python");
	}

</script>
<!--  End of file run.php -->