
<?php 
function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
$rid='';
$faculty_id='';
$subject_id='';
if(isset($_GET['rid']))
$rid = $_GET['rid'];
if(isset($_GET['fid']))
$faculty_id = $_GET['fid'];
if(isset($_GET['sid']))
$subject_id = $_GET['sid'];
$restriction = $conn->query("SELECT r.id,s.id as sid,f.id as fid,concat(f.firstname,' ',f.lastname) as faculty,s.code,s.subject FROM restriction_list r inner join faculty_list f on f.id = r.faculty_id inner join subject_list s on s.id = r.subject_id where academic_id ={$_SESSION['academic']['id']} and class_id = {$_SESSION['login_class_id']} and r.id not in (SELECT restriction_id from evaluation_list where academic_id ={$_SESSION['academic']['id']} and student_id = {$_SESSION['login_id']} ) ");
?>

<div class="col-lg-12">
	<div class="row">
		<div class="col-md-3">
			<div class="list-group">
				<?php 
				while($row=$restriction->fetch_array()):
					if(empty($rid)){
							$rid = $row['id'];
							$faculty_id = $row['fid'];
							$subject_id = $row['sid'];
						}
				?>
				<a class="list-group-item list-group-item-action <?php echo isset($rid) && $rid == $row['id'] ? 'active' : '' ?>" href="./index.php?page=evaluate&rid=<?php echo $row['id'] ?>&sid=<?php echo $row['sid'] ?>&fid=<?php echo $row['fid'] ?>"><?php echo ucwords($row['faculty']).' - ('.$row["code"].') '.$row['subject'] ?></a>
			<?php endwhile; ?>
			</div>
		</div>	
		<div class="col-md-9">
			<div class="card card-outline card-info">
				<div class="card-header">
					<b>Evaluation Questionnaire for Academic: <?php echo $_SESSION['academic']['year'].' '.(ordinal_suffix($_SESSION['academic']['semester'])) ?> </b>
					<div class="card-tools">
						<button class="btn btn-sm btn-flat btn-primary bg-gradient-primary mx-1" form="manage-evaluation">Submit Evaluation</button>
					</div>
				</div>
				<div class="card-body">
					<fieldset class="border border-info p-2 w-100">
					   <legend  class="w-auto">Rating L	egend</legend>
					   <p>5 = Strongly Agree, 4 = Agree, 3 = Uncertain, 2 = Disagree, 1 = Strongly Disagree</p>
					</fieldset>
					<form id="manage-evaluation"  method="POST" action="">
						<input type="hidden" name="class_id" value="<?php echo $_SESSION['login_class_id'] ?>">
						<input type="hidden" name="faculty_id" value="<?php echo $faculty_id?>">
						<input type="hidden" name="restriction_id" value="<?php echo $rid ?>">
						<input type="hidden" name="subject_id" value="<?php echo $subject_id ?>">
						<input type="hidden" name="academic_id" value="<?php echo $_SESSION['academic']['id'] ?>">
					<div class="clear-fix mt-2"></div>
					<?php 
							$q_arr = array();
						$criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
						while($crow = $criteria->fetch_assoc()):
					?>
					<table class="table table-condensed">
						<thead>
							<tr class="bg-gradient-secondary">
								<th class=" p-1"><b><?php echo $crow['criteria'] ?></b></th>
								<th class="text-center">1</th>
								<th class="text-center">2</th>
								<th class="text-center">3</th>
								<th class="text-center">4</th>
								<th class="text-center">5</th>
							</tr>
						</thead>
						<tbody class="tr-sortable">
							<?php 
							$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
							while($row=$questions->fetch_assoc()):
							$q_arr[$row['id']] = $row;
							?>
							<tr class="bg-white">
								<td class="p-1" width="40%">
									<?php echo $row['question'] ?>
									<input type="hidden" name="qid[]" value="<?php echo $row['id'] ?>">
								</td>
								<?php for($c=1;$c<=5;$c++): ?>
								<td class="text-center">
									<div class="icheck-success d-inline">
				                        <input type="radio" name="rate[<?php echo $row['id'] ?>]" <?php echo $c == 5 ? "checked" : '' ?> id="qradio<?php echo $row['id'].'_'.$c ?>" value="<?php echo $c ?>">
				                        <label for="qradio<?php echo $row['id'].'_'.$c ?>">
				                        </label>
			                      </div>
								</td>
								<?php endfor; ?>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php endwhile; ?>
					 <!-- Comment Textarea -->
					 <div class="form-group">
    <label for="comment">Comments:</label>
    <textarea name="comment" id="comment" class="form-control" placeholder="Enter your comments here..."></textarea>
</div>
					</form>
					<div class="form-group">
	
<button type="button" id="test-sentiment" class="btn btn-secondary">Test Sentiment</button>
<div id="sentiment-result" class="mt-3"></div>
<?php if (isset($sentimentResult)): ?>
    <div id="sentiment-result" class="mt-3">
        <h4>Sentiment Analysis Result:</h4>
        <p><strong>Sentiment:</strong> <?php echo htmlspecialchars($sentimentResult['sentiment']); ?></p>
        <p><strong>Polarity:</strong> <?php echo htmlspecialchars($sentimentResult['score']); ?></p>
        <p><strong>Subjectivity:</strong> <?php echo htmlspecialchars($sentimentResult['subjectivity']); ?></p>
        <p><strong>Subjectivity Label:</strong> <?php echo htmlspecialchars($sentimentResult['subjectivity_label']); ?></p>
    </div>
<?php elseif (isset($error)): ?>
    <div class="error-message">
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
<?php endif; ?>



				</div>
				
			</div>

		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		if('<?php echo $_SESSION['academic']['status'] ?>' == 0){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>not_started.php")
		}else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
		}
		else if('<?php echo $_SESSION['academic']['status'] ?>' == 2){
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>closed.php")
		}
		if(<?php echo empty($rid) ? 1 : 0 ?> == 1)
			uni_modal("Information","<?php echo $_SESSION['login_view_folder'] ?>done.php")
	})
	$('#manage-evaluation').submit(function(e){
  e.preventDefault();
  start_load();
  var commentText = $('#comment').val().trim();

  $.ajax({
    url:'ajax.php?action=save_evaluation',
    method:'POST',
    data:$(this).serialize(),
    success:function(resp){
      if(resp == 1){
        alert_toast("Data successfully saved.","success");
        setTimeout(function(){
          location.reload();
        },1750);
      } else {
        alert_toast("An error occurred.","error");
      }
      end_load();
    }
  });
});
	$('#test-sentiment').click(function(){
    var commentText = $('#comment').val().trim();
    if(commentText == ''){
        alert('Please enter a comment to analyze.');
        return;
    }
    start_load(); // Function to show a loading indicator
    $.ajax({
        url: 'ajax.php?action=test_sentiment',
        method: 'POST',
        data: {comment: commentText},
        dataType: 'json',
        success: function(resp){
            if(resp && !resp.error){
                var sentiment = resp.sentiment;
                var score = resp.score;
                var subjectivity = resp.subjectivity;
                var subjectivity_label = resp.subjectivity_label;
                var resultHtml = '<h4>Sentiment Analysis Result:</h4>';
                resultHtml += '<p><strong>Sentiment:</strong> '+sentiment+'</p>';
                resultHtml += '<p><strong>Sentiment Score:</strong> '+score.toFixed(2)+'</p>';
                resultHtml += '<p><strong>Subjectivity:</strong> '+subjectivity.toFixed(2)+'</p>';
                resultHtml += '<p><strong>Subjectivity Level:</strong> '+subjectivity_label+'</p>';
                $('#sentiment-result').html(resultHtml);
            } else {
                $('#sentiment-result').html('<p>'+ (resp.error || 'Error in sentiment analysis.') +'</p>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log("AJAX Error:", textStatus, errorThrown);
            console.log("Response Text:", jqXHR.responseText);
            $('#sentiment-result').html('<p>Error in sentiment analysis.</p>');
        },
        complete: function(){
            end_load(); // Function to hide the loading indicator
        }
    });
});

	// Function to calculate the score
function calculateScore() {
    var totalScore = 0;
    var maxScore = 0;

    // For each question, the maximum score is 5
    var numQuestions = $('input[name^="rate["][value="5"]').length;
    maxScore = numQuestions * 5;

    // Sum up the selected ratings
    $('input[name^="rate["]:checked').each(function(){
        totalScore += parseInt($(this).val());
    });

    // Calculate percentage
    var percentage = (totalScore / maxScore) * 100;

    // Update the results
    $('#total-score').text(totalScore);
    $('#max-score').text(maxScore);
    $('#percentage-score').text(percentage.toFixed(2));
}

// Call calculateScore on page load
$(document).ready(function(){
    calculateScore();

    // Call calculateScore whenever a radio button is changed
    $('input[name^="rate["]').change(function(){
        calculateScore();
    });
});

</script>
<?php
// ... existing PHP code ...

// Initialize variables
$sentimentResult = null;
$error = null;

// ajax.php
if (isset($_GET['action']) && $_GET['action'] == 'test_sentiment') {
    $comment = $_POST['comment'];
    if (!empty(trim($comment))) {
        // Escape the comment to prevent command injection
        $escapedComment = escapeshellarg($comment);

        // Paths to Python executable and script
        $pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe'; // To be changed
        $scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; // To be changed

        // Build the command
        $command = "\"$pythonExecutable\" \"$scriptPath\" $escapedComment";

        // Execute the command and capture the output
        $output = shell_exec($command);

        // Decode the JSON output
        $sentimentResult = json_decode($output, true);

        // Handle cases where the output is not valid JSON
        if ($sentimentResult === null) {
            echo json_encode(['error' => 'Error processing sentiment analysis.']);
        } else {
            echo json_encode($sentimentResult);
        }
    } else {
        echo json_encode(['error' => 'Please enter a comment to analyze.']);
    }
    exit;
}
// ... rest of your PHP code ...
?>