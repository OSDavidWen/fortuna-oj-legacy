		<script src="/application/third_party/ckeditor/ckeditor.js"></script>
		<script src="/application/third_party/ckfinder/ckfinder.js"></script>

<h4>Problem Statements <a title="Click empty frames to edit!" id="editor_help"><i class="icon-question-sign"></i></a></h4>
<hr />

<?=validation_errors()?>

<div class="addproblem_form">

	<form class="form-horizontal" action="/index.php/admin/addproblem/<?=isset($pid) ? $pid : ''?>" method="post" id="addproblem">
	
		<input type="hidden" name="title" id="title_data" />
		<fieldset>
			<legend><h5>Title</h5></legend>
			<h3><div class="well" id="title" style="padding: 5px; text-align:center" contenteditable="true">
				<?=set_value('title', isset($title) ? $title : '', false)?>
			</div></h3>
		</fieldset>

		<textarea id="problemDescription_data" name="problemDescription" style="display:none"></textarea>
		<fieldset>
			<legend><h5>Description</h5></legend>
			<div class="well" id="problemDescription" contenteditable="true">
				<?=set_value('problemDescription', isset($problemDescription) ? html_entity_decode($problemDescription) : '', false)?>
			</div>
		</fieldset>

		<textarea id="inputDescription_data" name="inputDescription" style="display:none"></textarea>
		<fieldset class="span6" style="margin-left:0; clear:both">
			<legend><h5>Input</h5></legend>
			<div class="well" id="inputDescription" contenteditable="true">
				<?=set_value('inputDescription', isset($inputDescription) ? $inputDescription : '', false)?>
			</div>
		</fieldset>
		
		<textarea id="outputDescription_data" name="outputDescription" style="display:none"></textarea>
		<fieldset class="span6">
			<legend><h5>Output</h5></legend>
			<div class="well" id="outputDescription" contenteditable="true">
				<?=set_value('outputDescription', isset($outputDescription) ? $outputDescription : '', false)?>
			</div>
		</fieldset>
		<div class="clearfix"></div>
		
		<textarea id="inputSample_data" name="inputSample" style="display:none"></textarea>
		<fieldset class="span6" style="margin-left:0">
			<legend><h5>Sample Input</h5></legend>
			<div class="well" id="inputSample" contenteditable="true">
				<?=set_value('inputSample', isset($inputSample) ? $inputSample : '', false)?>
			</div>
		</fieldset>
		
		<textarea id="outputSample_data" name="outputSample" style="display:none"></textarea>
		<fieldset class="span6">
			<legend><h5>Sample Output</h5></legend>
			<div class="well" id="outputSample" contenteditable="true">
				<?=set_value('outputSample', isset($outputSample) ? $outputSample : '', false)?>
			</div>
		</fieldset>
		<div class="clearfix"></div>
		
		<textarea id="dataConstraint_data" name="dataConstraint" style="display:none"></textarea>
		<fieldset>
			<legend><h5>Data Constraint</h5></legend>
			<div class="well" id="dataConstraint" contenteditable="true">
				<?=set_value('dataConstraint', isset($dataConstraint) ? $dataConstraint : '', false)?>
			</div>
		</fieldset>
		
		<textarea id="hint_data" name="hint" style="display:none"></textarea>
		<fieldset>
			<legend><h5>Hint</h5></legend>
			<div class="well" id="hint" contenteditable="true">
				<?=set_value('hint', isset($hint) ? $hint : '', false)?>
			</div>
		</fieldset>
			
			<label for="source" class="control-label">Source</label>
			<div class="controls controls-row">
				<input type="text" id="source" name="source"  class="input-xxlarge" value="<?=set_value('source', isset($source) ? $source : '', false)?>"/>
			</div>
			
			<label for="codeSizeLimit" class="control-label">Source Code Limit (KB)</label>
			<div class="controls controls-row">
				<input type="text" class="input-mini" id="codeSizeLimit" name="codeSizeLimit" value="<?=set_value('codeSizeLimit', isset($codeSizeLimit) ? $codeSizeLimit : '')?>"/>
			</div>
		<div>
		
		<button type="submit" class="btn btn-primary pull-right" onclick="return add_problem()">Save</button>
		
		<script type="text/javascript">
			CKEDITOR.basePath = "/application/third_party/ckeditor/";
			$(document).ready(function(){
				$('#editor_help').tooltip({placement: 'bottom'});
			});
		
			CKEDITOR.disableAutoInline = true;
			CKEDITOR.config.forcePasteAsPlainText = true;
			CKEDITOR.config.htmlEncodeOutpuy = true;
			CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
			CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_BR;
			var td = CKEDITOR.inline('title');
			CKEDITOR.instances.title.setMode('source');
			CKEDITOR.instances.title.blockless = true;
			var pd = CKEDITOR.inline('problemDescription');
			var id = CKEDITOR.inline('inputDescription');
			var od = CKEDITOR.inline('outputDescription');
			var sid = CKEDITOR.inline('inputSample');
			var sod = CKEDITOR.inline('outputSample');
			var dc = CKEDITOR.inline('dataConstraint');
			var hint = CKEDITOR.inline('hint');
 			CKFinder.setupCKEditor(pd, "/application/third_party/ckfinder/");
 			CKFinder.setupCKEditor(id, "/application/third_party/ckfinder/");
 			CKFinder.setupCKEditor(od, "/application/third_party/ckfinder/");
 			CKFinder.setupCKEditor(dc, "/application/third_party/ckfinder/");
 			CKFinder.setupCKEditor(hint, "/application/third_party/ckfinder/");
			
			function add_problem(){
				$('#title_data').val(td.getData());
				$('#problemDescription_data').val(pd.getData());
				$('#inputDescription_data').val(id.getData());
				$('#outputDescription_data').val(od.getData());
				$('#inputSample_data').val(sid.getData());
				$('#outputSample_data').val(sod.getData());
				$('#dataConstraint_data').val(dc.getData());
				$('#hint_data').val(hint.getData());

				$('#addproblem').ajaxSubmit({
					success: function(responseText, stautsText){
						status = responseText.substr(0, 7);
						responseText = responseText.substr(7);
						if (status == 'success'){
							if (responseText == '') load_page('admin/problemset');
							else load_page('admin/dataconf/' + responseText);
						} else $('#page_content').html(responseText);
					}
				});
				
				return false;
			}
		</script>
	</form>
</div>