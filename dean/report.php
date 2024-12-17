<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}


// Rest of your code...

$faculty_id = isset($_GET['fid']) ? $_GET['fid'] : '';
$department_id = $_SESSION['login_department_id']; // Dean's department ID

// Check if the faculty belongs to the Dean's department
if (!empty($faculty_id)) {
	$faculty_check = $conn->query("SELECT id FROM faculty_list WHERE id = '$faculty_id' AND department_id = '$department_id'");
	if ($faculty_check->num_rows == 0) {
		// Faculty not in Dean's department
		echo "<h4>You do not have permission to view reports for this faculty.</h4>";
		exit;
	}
}

function ordinal_suffix($num)
{
	$num = $num % 100; // protect against large numbers
	if ($num < 11 || $num > 13) {
		switch ($num % 10) {
			case 1:
				return $num . 'st';
			case 2:
				return $num . 'nd';
			case 3:
				return $num . 'rd';
		}
	}
	return $num . 'th';
}

$faculty = $conn->query("SELECT *, CONCAT(firstname,' ',lastname) as name FROM faculty_list WHERE department_id = '$department_id' ORDER BY CONCAT(firstname,' ',lastname) ASC");
$f_arr = array();
$fname = array();
while ($row = $faculty->fetch_assoc()) {
	$f_arr[$row['id']] = $row;
	$fname[$row['id']] = ucwords($row['name']);
}
?>
<style>
	#polarityChart,
	#subjectivityChart {
		width: 200px !important;
		height: 200px !important;
	}

	/* Add the rates class styling here */
	.rates {
		color: black;
	}
</style>
<div class="col-lg-12">
	<div class="callout callout-info">
		<div class="d-flex w-100 justify-content-center align-items-center">
			<label for="faculty">Select Faculty</label>
			<div class=" mx-2 col-md-4">
				<select name="" id="faculty_id" class="form-control form-control-sm select2">
					<option value=""></option>
					<?php
					foreach ($f_arr as $row):
					?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($faculty_id) && $faculty_id == $row['id'] ? "selected" : "" ?>><?php echo ucwords($row['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-1">
			<div class="d-flex justify-content-end w-100">
				<button class="btn btn-sm btn-success bg-gradient-success" style="display:none" id="print-btn"><i class="fa fa-print"></i> Print</button>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="callout callout-info">
				<div class="list-group" id="class-list">

				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="callout callout-info" id="printable">
				<div>
					<h3 class="text-center">Evaluation Report</h3>
					<hr>
					<table width="100%">
						<tr>
							<td width="50%">
								<p><b>Faculty: <span id="fname"></span></b></p>
							</td>
							<td width="50%">
								<p><b>Academic Year: <span id="ay"><?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix($_SESSION['academic']['semester'])) ?> Semester</span></b></p>
							</td>
						</tr>
						<tr>
							<td width="50%">
								<p><b>Class: <span id="classField"></span></b></p>
							</td>
							<td width="50%">
								<p><b>Subject: <span id="subjectField"></span></b></p>
							</td>
						</tr>
					</table>
					<p class=""><b>Total Student Evaluated: <span id="tse"></span></b></p>
				</div>
				<fieldset class="border border-info p-2 w-100">
					<legend class="w-auto">Rating Legend</legend>
					<p>5 = Strongly Agree, 4 = Agree, 3 = Uncertain, 2 = Disagree, 1 = Strongly Disagree</p>
				</fieldset>
				<?php
				$q_arr = array();
				$criteria = $conn->query("SELECT * FROM criteria_list where id in (SELECT criteria_id FROM question_list where academic_id = {$_SESSION['academic']['id']} ) order by abs(order_by) asc ");
				while ($crow = $criteria->fetch_assoc()):
				?>
					<table class="table table-condensed wborder">
						<thead>
							<tr class="bg-gradient-secondary">
								<th class=" p-1"><b><?php echo $crow['criteria'] ?></b></th>
								<th width="5%" class="text-center">1</th>
								<th width="5%" class="text-center">2</th>
								<th width="5%" class="text-center">3</th>
								<th width="5%" class="text-center">4</th>
								<th width="5%" class="text-center">5</th>
							</tr>
						</thead>
						<tbody class="tr-sortable">
							<?php
							$questions = $conn->query("SELECT * FROM question_list where criteria_id = {$crow['id']} and academic_id = {$_SESSION['academic']['id']} order by abs(order_by) asc ");
							while ($row = $questions->fetch_assoc()):
								$q_arr[$row['id']] = $row;
							?>
								<tr class="bg-white">
									<td class="p-1" width="40%">
										<?php echo $row['question'] ?>
									</td>
									<?php for ($c = 1; $c <= 5; $c++): ?>
										<td class="text-center">
											<span class="rate_<?php echo $c . '_' . $row['id'] ?> rates"></span>
											<div id="dynamic-content"></div>
			</div>
			</td>
		<?php endfor; ?>
		</tr>
	<?php endwhile; ?>
	</tbody>
	</table>
<?php endwhile; ?>
		</div>
	</div>
</div>
</div>
<style>
	.list-group-item:hover {
		color: black !important;
		font-weight: 700 !important;
	}
</style>
<noscript>
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
		}

		table.wborder tr,
		table.wborder td,
		table.wborder th {
			border: 1px solid gray;
			padding: 3px
		}

		table.wborder thead tr {
			background: #6c757d linear-gradient(180deg, #828a91, #6c757d) repeat-x !important;
			color: #fff;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.text-left {
			text-align: left;
		}
	</style>
</noscript>
<script>
	$(document).ready(function() {
		$('#faculty_id').change(function() {
			if ($(this).val() > 0) {
				window.location.href = './index.php?page=report&fid=' + $(this).val();
			} else {
				window.location.href = './index.php?page=report';
			}
		});
		if ($('#faculty_id').val() > 0)
			load_class()
	})

	function load_class() {
		start_load()
		var fname = <?php echo json_encode($fname) ?>;
		$('#fname').text(fname[$('#faculty_id').val()])
		$.ajax({
			url: "ajax.php?action=get_class",
			method: 'POST',
			data: {
				fid: $('#faculty_id').val()
			},
			error: function(err) {
				console.log(err)
				alert_toast("An error occured", 'error')
				end_load()
			},
			success: function(resp) {
				if (resp) {
					resp = JSON.parse(resp)
					if (Object.keys(resp).length <= 0) {
						$('#class-list').html('<a href="javascript:void(0)" class="list-group-item list-group-item-action disabled">No data to be display.</a>')
					} else {
						$('#class-list').html('')
						Object.keys(resp).map(k => {
							$('#class-list').append('<a href="javascript:void(0)" data-json=\'' + JSON.stringify(resp[k]) + '\' data-id="' + resp[k].id + '" class="list-group-item list-group-item-action show-result">' + resp[k].class + ' - ' + resp[k].subj + '</a>')
						})

					}
				}
			},
			complete: function() {
				end_load()
				anchor_func()
				if ('<?php echo isset($_GET['rid']) ?>' == 1) {
					$('.show-result[data-id="<?php echo isset($_GET['rid']) ? $_GET['rid'] : '' ?>"]').trigger('click')
				} else {
					$('.show-result').first().trigger('click')
				}
			}
		})
	}

	function anchor_func() {
		$('.show-result').click(function() {
			var vars = [],
				hash;
			var data = $(this).attr('data-json')
			data = JSON.parse(data)
			var _href = location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for (var i = 0; i < _href.length; i++) {
				hash = _href[i].split('=');
				vars[hash[0]] = hash[1];
			}
			window.history.pushState({}, null, './index.php?page=report&fid=' + vars.fid + '&rid=' + data.id);
			load_report(vars.fid, data.sid, data.id);
			$('#subjectField').text(data.subj)
			$('#classField').text(data.class)
			$('.show-result.active').removeClass('active')
			$(this).addClass('active')
		})
	}


// Declare global variables for charts
var sentimentChart;

function load_report(faculty_id, subject_id, class_id) {
    // Clear previous report data
    $('.rates').text('');
    $('#tse').text('');
    $('#print-btn').hide();
    $('#total-average').remove(); // Remove if already exists
    $('#average-sentiment').remove(); // Remove if already exists
    $('#sentiment-distribution').remove();
    $('#comments-section').remove();

    if ($('#preloader2').length <= 0)
        start_load();

    $.ajax({
        url: 'ajax.php?action=get_report',
        method: "POST",
        data: {
            faculty_id: faculty_id,
            subject_id: subject_id,
            class_id: class_id
        },
        error: function(err) {
            console.log(err);
            alert_toast("An Error Occurred.", "error");
            end_load();
        },
        success: function(resp) {
            if (resp) {
                try {
                    resp = JSON.parse(resp);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.log('Response:', resp);
                    alert_toast("Invalid response from server.", "error");
                    end_load();
                    return;
                }
                if (Object.keys(resp).length <= 0) {
                    $('.rates').text('');
                    $('#tse').text('');
                    $('#print-btn').hide();
                    alert_toast("No evaluation data available for the selected faculty and class.", 'info');
                } else {
                    $('#print-btn').show();
                    $('#tse').text(resp.tse);
                    $('.rates').text('-');
                    var data = resp.data;
                    var totalSum = 0;
                    var totalQuestions = 0;
                    Object.keys(data).map(function(q) {
                        var average_q = 0;
                        Object.keys(data[q]).map(function(r) {
                            $('.rate_' + r + '_' + q).text(data[q][r] + '%');
                            var percentage = data[q][r]; // percentage as a number
                            var rating = parseInt(r);
                            average_q += (percentage / 100) * rating;
                        });
                        totalSum += average_q;
                        totalQuestions++;
                    });
                    var totalAverage = totalSum / totalQuestions;
                    // Display total average
                    $('#total-average').remove(); // Remove if already exists
                    $('#printable').append('<div id="total-average" style="margin-top:20px;"><h4><b>Total Average:</b> ' + totalAverage.toFixed(2) + '</h4></div>');

                    // Now display average polarity and subjectivity
                    var avg_polarity = parseFloat(resp.avg_polarity);
                    var avg_subjectivity = parseFloat(resp.avg_subjectivity);

                    // Determine sentiment label based on avg_polarity
                    var sentimentLabel = '';
                    var score = avg_polarity; // avg_polarity ranges from -1 to 1
                    // Normalize polarity to 0 - 1
                    var normalized_polarity = (score + 1) / 2;

                    if (0.0 <= normalized_polarity && normalized_polarity < 0.1) {
                        sentimentLabel = 'Very Strong (Negative)';
                    } else if (0.1 <= normalized_polarity && normalized_polarity < 0.3) {
                        sentimentLabel = 'Strong (Negative)';
                    } else if (0.3 <= normalized_polarity && normalized_polarity < 0.4) {
                        sentimentLabel = 'Moderate (Negative)';
                    } else if (0.4 <= normalized_polarity && normalized_polarity < 0.6) {
                        sentimentLabel = 'Neutral';
                    } else if (0.6 <= normalized_polarity && normalized_polarity < 0.7) {
                        sentimentLabel = 'Moderate (Positive)';
                    } else if (0.7 <= normalized_polarity && normalized_polarity < 0.9) {
                        sentimentLabel = 'Strong (Positive)';
                    } else if (0.9 <= normalized_polarity && normalized_polarity <= 1.0) {
                        sentimentLabel = 'Very Strong (Positive)';
                    } else {
                        sentimentLabel = 'Unknown';
                    }

                    // Similarly for subjectivity
                    var subjectivityLabel = '';
                    var subj_score = avg_subjectivity;

                    if (0.0 <= subj_score && subj_score < 0.10) {
                        subjectivityLabel = 'Highly Objective';
                    } else if (0.10 <= subj_score && subj_score < 0.30) {
                        subjectivityLabel = 'Objective';
                    } else if (0.30 <= subj_score && subj_score < 0.45) {
                        subjectivityLabel = 'Slightly Objective';
                    } else if (0.45 <= subj_score && subj_score < 0.55) {
                        subjectivityLabel = 'Neutral';
                    } else if (0.55 <= subj_score && subj_score < 0.70) {
                        subjectivityLabel = 'Slightly Subjective';
                    } else if (0.70 <= subj_score && subj_score < 0.85) {
                        subjectivityLabel = 'Subjective';
                    } else if (0.85 <= subj_score && subj_score <= 1.0) {
                        subjectivityLabel = 'Highly Subjective';
                    } else {
                        subjectivityLabel = 'Unknown';
                    }

                    // Calculate percentages
                    var avg_polarity_percent = avg_polarity * 100;
                    var avg_subjectivity_percent = avg_subjectivity * 100;

                    // Append the new content with canvas elements for charts
                    $('#printable').append(
                        '<div id="average-sentiment" style="margin-top:20px;">' +
                        '<h4><b>Average Sentiment Scores:</b></h4>' +
                        '<div style="display:flex; justify-content: space-around; align-items: center;">' +
                        '<div>' +
                        '<p><b>Average Polarity:</b> ' + avg_polarity.toFixed(2) + '</p>' +
                        '<canvas id="polarityChart" width="200" height="200"></canvas>' +
                        '<p><b>Sentiment Label:</b> ' + sentimentLabel + '</p>' +
                        '</div>' +
                        '<div>' +
                        '<p><b>Average Subjectivity:</b> ' + avg_subjectivity.toFixed(2) + '</p>' +
                        '<canvas id="subjectivityChart" width="200" height="200"></canvas>' +
                        '<p><b>Subjectivity Label:</b> ' + subjectivityLabel + '</p>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    );

                    // Create the Polarity Chart
                    var ctxP = document.getElementById('polarityChart').getContext('2d');
                    var polarityChart = new Chart(ctxP, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [Math.abs(avg_polarity_percent), 100 - Math.abs(avg_polarity_percent)],
                                backgroundColor: [
                                    avg_polarity >= 0 ? 'green' : 'red',
                                    'lightgray'
                                ]
                            }]
                        },
                        options: {
                            cutoutPercentage: 80, // Adjust the thickness of the donut
                            rotation: -Math.PI / 2,
                            circumference: Math.PI * 2,
                            tooltips: {
                                enabled: false
                            }
                        }
                    });

                    // Create the Subjectivity Chart
                    var ctxS = document.getElementById('subjectivityChart').getContext('2d');
                    var subjectivityChart = new Chart(ctxS, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [avg_subjectivity_percent, 100 - avg_subjectivity_percent],
                                backgroundColor: [
                                    'orange',
                                    'lightgray'
                                ]
                            }]
                        },
                        options: {
                            cutoutPercentage: 80,
                            rotation: -Math.PI / 2,
                            circumference: Math.PI * 2,
                            tooltips: {
                                enabled: false
                            }
                        }
                    });

                    // Process the sentiment counts and create the 7-bar chart
                    var sentimentCounts = resp.sentiment_counts;
                    var sentimentLabels = [
                        'Very Strong (Negative)',
                        'Strong (Negative)',
                        'Moderate (Negative)',
                        'Neutral',
                        'Moderate (Positive)',
                        'Strong (Positive)',
                        'Very Strong (Positive)'
                    ];
                    var sentimentData = sentimentLabels.map(function(label) {
                        return sentimentCounts[label];
                    });

                    // Remove previous chart if exists
                    if (sentimentChart) {
                        sentimentChart.destroy();
                    }

                    // Append new div with canvas
                    $('#printable').append(
                        '<div id="sentiment-distribution" style="margin-top:20px;">' +
                        '<h4><b>Sentiment Distribution:</b></h4>' +
                        '<canvas id="sentimentChart" width="600" height="400"></canvas>' +
                        '</div>'
                    );

                    // Colors for the bars
                    var barColors = [
                        '#4575b4',
                            '#4575b4',
                            '#4575b4',
                            '#4575b4',
                            '#4575b4',
                            '#4575b4',
                            '#4575b4'
                    ];

                    var ctxSentiment = document.getElementById('sentimentChart').getContext('2d');
                    sentimentChart = new Chart(ctxSentiment, {
                        type: 'bar',
                        data: {
                            labels: sentimentLabels,
                            datasets: [{
                                label: 'Number of Comments',
                                data: sentimentData,
                                backgroundColor: barColors
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: true
                        }
                    });

                    // Handle comments
                    if (resp.comments && resp.comments.length > 0) {
                        display_comments(resp.comments);
                    } else {
                        $('#comments-section').remove();
                    }
                }
            }
        },
        complete: function() {
            end_load();
        }
    });
}

$('#print-btn').click(function() {
    start_load();

    // Capture the chart images as base64
    var charts = document.querySelectorAll('canvas');
    var totalCharts = charts.length;
    var chartsProcessed = 0;
    var chartImages = [];

    // Loop through each chart and convert it to an image (base64)
    charts.forEach(function(chart, index) {
        var img = new Image();
        img.src = chart.toDataURL(); // Convert canvas to base64 image
        img.onload = function() {
            // Store the image in chartImages array
            chartImages.push(img);

            // Once all charts are processed, proceed with printing
            chartsProcessed++;
            if (chartsProcessed === totalCharts) {
                // Proceed to generate printable content
                generatePrintableContent(chartImages);
            }
        };
    });

    function generatePrintableContent(chartImages) {
        // Clone the printable content and replace canvas elements with images
        var ns = $('noscript').clone();
        var content = $('#printable').html();

        // Find all canvas elements and replace them with the corresponding images
        var canvases = document.querySelectorAll('canvas');

        canvases.forEach(function(canvas, index) {
            // Generate the <img> tag to replace each <canvas>
            var imgTag = `<img src="${chartImages[index].src}" alt="Chart ${index + 1}" style="display:block; max-width:100%; height:auto;">`;
            content = content.replace(canvas.outerHTML, imgTag);  // Replace canvas with corresponding image
        });

        // Create a new window for printing
        var nw = window.open("Report", "_blank", "width=900,height=700");
        nw.document.write(content);
        nw.document.close();

        // Wait for the document to be fully loaded before printing
        setTimeout(function() {
            nw.print();
            setTimeout(function() {
                nw.close();
                end_load();
            }, 750);
        }, 500);
    }
});
function display_comments(comments) {
        $('#comments-section').remove();

        var commentsHtml = '<div id="comments-section" style="margin-top:20px;">' +
            '<h4><b>Student Comments:</b></h4>';

        comments.forEach(function(comment) {
            commentsHtml +=
                '<div class="comment-item card mb-3">' +
                '<div class="card-body">' +
                '<h5 class="card-title">Sentiment: ' + comment.sentiment + '</h5>' +
                '<p class="card-text">' + comment.comment + '</p>' +
                '</div>' +
                '</div>';
        });

        commentsHtml += '</div>';

        $('#printable').append(commentsHtml);
    }

</script>

<style>
    #comments-section {
        margin-top: 20px;
    }

    .comment-item {
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .comment-item .card-body {
        padding: 15px;
    }

    .comment-item .card-title {
        margin-bottom: 10px;
        font-size: 18px;
        color: #333;
    }

    .comment-item .card-text {
        font-size: 16px;
        color: #555;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printable, #printable * {
            visibility: visible;
        }

        #printable {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print, .no-print * {
            display: none !important;
        }
    }

    #sentimentChartContainer {
        max-width: 800px;
        margin: 0 auto;
    }

    #sentimentChart {
        width: 100% !important;
        height: auto !important;
    }
</style>
<style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --font-family: 'Inter', sans-serif;
        }

    
        .card-header {
            background-color: var(--primary-color);
            color: #fff;
        }

        .list-group-item {
            transition: background-color 0.3s, color 0.3s;
        }

        .list-group-item:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        .list-group-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .rating-label {
            color: #000; /* Ensuring labels 1-5 are black */
            font-weight: 600;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .badge-primary {
            background-color: var(--primary-color);
        }

        .alert-success, .alert-danger, .alert-info, .alert-warning {
            border-radius: 0.5rem;
        }

        .table thead th {
            background-color: var(--secondary-color);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Smooth transitions for interactive elements */
        .btn, .list-group-item, .form-check-input {
            transition: all 0.3s ease;
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-color);
        }

        ::-webkit-scrollbar-thumb {
            background-color: var(--secondary-color);
            border-radius: 4px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.25rem;
            }

            .btn {
                width: 100%;
                margin-top: 10px;
            }

            .teacher-card-link {
                width: 100%;
            }
        }

        /* Teacher Card Styles */
        .teacher-card-link {
            text-decoration: none;
            color: inherit;
            width: 100%;
        }

        .teacher-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            width: 250px;
            height: 200px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .teacher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .icon-container {
            background-color: var(--primary-color);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon {
            color: #fff;
            font-size: 24px;
        }

        .message-text {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .sub-text {
            font-size: 16px;
            color: var(--secondary-color);
        }

        /* Welcome Card Styles */
        .welcome-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .welcome-card h4 {
            color: var(--dark-color);
            font-weight: 600;
        }

        .academic-year, .evaluation-status {
            color: #000; /* Changed from yellow to black for better readability */
            font-weight: bold;
            font-size: 1.2em;
        }

        .academic-year::after, .evaluation-status::after {
            content: "";
            display: block;
            height: 2px;
            background: var(--primary-color);
            margin-top: 4px;
            width: 50px;
        }

        /* Print Button */
        #print-btn {
            display: none;
        }

        /* Custom Rates Styling */
        .rates {
            color: black;
            font-weight: 600;
        }

        /* Additional Styles */
        #polarityChart,
        #subjectivityChart {
            width: 200px !important;
            height: 200px !important;
        }

        /* Noscript Styles */
        noscript table {
            width: 100%;
            border-collapse: collapse;
        }

        noscript table.wborder tr,
        noscript table.wborder td,
        noscript table.wborder th {
            border: 1px solid gray;
            padding: 8px;
        }

        noscript table.wborder thead tr {
            background: #6c757d;
            color: #fff;
        }

        noscript .text-center {
            text-align: center;
        }

        noscript .text-right {
            text-align: right;
        }

        noscript .text-left {
            text-align: left;
        }
    </style>