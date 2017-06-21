<?php
include "w_top.php";
?>
<div class="container">
	<form action="send_message_function.php" method="post" class="form-horizontal">
		<input type="hidden" name="process" id="process" value="Y">
		<div class="form-group">
			<label class="control-label col-xs-2">Token Access:</label>
			<div class="col-xs-8">
				<input type="text" name="token_access" id="token_access" value="VGO54TpsjKQPB2fpcY02n2SbfETsnV6bNxZPdaeLgohtqwi7wnNl6xF+9zgA5xiv8xZhkUTBjg1Hgog0E23gvI86et1O1YHqbjJZw7FEzScidVC3J7no8vS6U0oFeeuYFei0IxF1tWcOFpTxJb5z5AdB04t89/1O/w1cDnyilFU=" class="form-control" required>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">User ID:</label>
			<div class="col-xs-8">
				<input type="text" name="user_id" id="user_id" value="U3842f58385e2fa1ed44500796e3ec2de" class="form-control" required	>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">Message:</label>
			<div class="col-xs-8">
				<textarea name="message" id="message" rows="3" class="form-control" required></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-12 text-center">
				<button class="btn btn-success btn-lg">ส่งข้อความ</button>
			</div>
		</div>
	</form>

</div>

<?php include "w_bottom_js.php";?>
<?php include "w_bottom.php";?>