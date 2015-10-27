@extends('global')

@section('title', 'Time Lapse - Food Produced')

@section('headerSuffix')
	<link href="/js/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
@stop

@section('footerSuffix')
	<!-- jQuery UI -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-us-mill-en.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-us-lcc-en.js"></script>
	<script type="text/javascript">
	<!--
		jQuery(document).on('ready', function() {
			var years = commodities = elems = [];
			var jsonData = jQuery.ajax({
				url: "/ajax/time/getFoodProductionData",
				method: "GET",
				success: function(result) {
					//console.log(result);
					var dataReturned = JSON.parse(result);
					jsonData = dataReturned;
					jQuery.each(dataReturned['income'], function(index, value) {
						years.push(index);
					});
					years.sort(function(a, b){return b-a});
					jQuery.each(years, function(index, value) {
						jQuery(".yearSelectors").append('<option value="' + value + '">' + value + '</option>');
					});
					/*
					jQuery.each(dataReturned['commodities'], function(index, value) {
						jQuery(".commoditySelectors").append('<option value="' + index + '">' + index + '</option>');
					});
					*/
					jQuery(".yearSelectors, .stateSelectors").val("");
					jQuery("#loading_wrap").fadeOut('fast');
					return dataReturned;
				}
			});
			var yearValue = jQuery("#yearSelector").val();
			var stateValue = jQuery("#stateSelector").val();
			var mapObject = new jvm.MultiMap({
				container: $('#world-map'),
				maxLevel: 1,
				main: {
					map: 'us_lcc_en',
					backgroundColor: "transparent",
					regionStyle: {
						initial: {
							fill: '#e4e4e4',
							"fill-opacity": 0.9,
							stroke: 'none',
							"stroke-width": 0,
							"stroke-opacity": 0
						}
					},
					series: {
						regions: [{
							scale: ["#BAE8DF","#0A483B"],
							attribute: "fill",
							min: "0",
							max: "1000000"
						}]
					},
				  onRegionTipShow: function(event, label, code){
					  if (yearValue == "") {
						label.html(
						  '<b>'+label.html()+'</b></br>'+
						  '<b>Select a year to load data</b>'
						);
					  }
					  else {
							label.html(
							  '<b>'+label.html()+'</b></br><b>Total Production</b> '+
							  jsonData['totals'][yearValue][code] + " lbs"
							);
					  }
				  }
				},
				mapUrlByCode: function(code, multiMap){
					return '/js/plugins/jvectormap/us/jquery-jvectormap-data-'+
					 code.toLowerCase()+'-'+
					 multiMap.defaultProjection+'-en.js';
				}
			});
			
			var mapLoaded = mapObject.params.main.map;
			var mapLoadedObj = mapObject.maps[mapLoaded];
			
			function addBreakdownData() {
				var stateVal = jQuery("#stateSelector").val();
				if (stateVal != "" && yearValue != "") {
					var html = '<li><b>Estimated Ag Income</b> $' + jsonData['income'][yearValue][stateVal] + '</li><li><b>Total Production</b> ' + jsonData['totals'][yearValue][stateVal] + ' lbs</li>';
					jQuery.each(jsonData['production'][yearValue][stateVal], function(index, value) {
						html = html + '<li><b>' + index + '</b><ul class="unstyled">';
						jQuery.each(value, function(subIndex, subValue) {
							html = html + '<li><b>' + subIndex + '</b> ' + subValue['produced'] + ' lbs</li>';
						});
						html = html + '</ul></li>';
					});
					jQuery("ul.stateDetails").html(html);
				}
				else {
					jQuery("ul.stateDetails").html('');
				}
			}
			jQuery("body").on('change', "#yearSelector", function(e) {
				e.preventDefault();
				yearValue = jQuery("#yearSelector").val();
				mapLoadedObj.series.regions[0].setValues(jsonData['totals'][yearValue]);
				if (stateValue != "") {
					addBreakdownData();
				}
			});
			jQuery("body").on('change', '#stateSelector', function(e) {
				e.preventDefault();
				stateValue = jQuery("#stateSelector").val();
				if (yearValue != "") {
					addBreakdownData();
				}
			});
			
			function drawNewLineChart(dataIn) {
				$("#canvas-wrapper").html("").html('<canvas id="lineChart" height="140"></canvas>');
				var lineData = {
					labels: ["2007", "2008", "2009", "2010", "2011", "2012", "2013", "2014"],
					datasets: dataIn['datasets']
				};

				var lineOptions = {
					scaleShowGridLines: true,
					tooltipTemplate: "<%if (label){%><%=label%>: <%}%> test <%= value %>",
					multiTooltipTemplate: "<%=datasetLabel%> : <%= value %>",
					scaleGridLineColor: "rgba(0,0,0,.05)",
					scaleGridLineWidth: 1,
					bezierCurve: true,
					bezierCurveTension: 0.4,
					pointDot: true,
					pointDotRadius: 4,
					pointDotStrokeWidth: 1,
					pointHitDetectionRadius: 20,
					datasetStroke: true,
					datasetStrokeWidth: 2,
					datasetFill: false,
					showScale: true,
					responsive: true,
				};

				var ctx = document.getElementById("lineChart").getContext("2d");
				var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
				return myNewChart;
			}
			jQuery("#productionPerState").on('change', 'select.stateSelectors', function(e) {
				e.preventDefault();
				var yrs =  ["2007", "2008", "2009", "2010", "2011", "2012", "2013", "2014"];
				var stateVal = jQuery(this).val();
				var html = '';
				var yrElems =[];
				
				jQuery.each(jsonData['cropJoints'][stateVal], function(indx, val) {
					html = html + '<option value="' + indx + '">' + indx + '</option>';
				});
				jQuery(".cropSelection").html(html);
				var options = jQuery(".cropSelection option");
				options.sort(function(a,b) {
					if (a.text > b.text) return 1;
					else if (a.text < b.text) return -1;
					else return 0
				});
				jQuery(".cropSelection").empty().append(options);
			});
			var last_valid_selection = null;

			 jQuery('.cropSelection').change(function(event) {
				if ($(this).val().length > 5) {
				  alert('You can only choose 5!');
				  $(this).val(last_valid_selection);
				} else {
				  last_valid_selection = $(this).val();
				}
				var stateV = jQuery("#productionPerState select.stateSelectors").val();
				var combined = {'datasets': [], 'data': [], 'label': []};
				var randomColorGenerator = function () { 
					return '#' + (Math.random().toString(16) + '0000000').slice(2, 8); 
				};
				jQuery.each(jQuery(this).val(), function(index, val) {
					combined['data'][index] = [jsonData['cropJoints'][stateV][val]["2007"], jsonData['cropJoints'][stateV][val]["2008"], jsonData['cropJoints'][stateV][val]["2009"], jsonData['cropJoints'][stateV][val]["2010"], jsonData['cropJoints'][stateV][val]["2011"], jsonData['cropJoints'][stateV][val]["2012"], jsonData['cropJoints'][stateV][val]["2013"], jsonData['cropJoints'][stateV][val]["2014"]];
					combined['label'][index] = val;
					var randomColor = randomColorGenerator();
					combined['datasets'][index] =
						{
							label: val,
							fillColor: randomColor,
							strokeColor: randomColor,
							pointColor: randomColor,
							pointStrokeColor: "#fff",
							pointHighlightFill: "#fff",
							pointHighlightStroke: randomColor,
							data: [jsonData['cropJoints'][stateV][val]["2007"], jsonData['cropJoints'][stateV][val]["2008"], jsonData['cropJoints'][stateV][val]["2009"], jsonData['cropJoints'][stateV][val]["2010"], jsonData['cropJoints'][stateV][val]["2011"], jsonData['cropJoints'][stateV][val]["2012"], jsonData['cropJoints'][stateV][val]["2013"], jsonData['cropJoints'][stateV][val]["2014"]]
						};
				});
				var myNewChart = drawNewLineChart(combined);
			  });
		});
	//-->
	</script>
@stop


@section('content')
<div id='loading_wrap' style='position:fixed; height:100%; width:100%; overflow:hidden; top:0; left:0;z-index:9999; background:rgba(255,255,255,0.85)'><div class="spiner-example"><div class="sk-spinner sk-spinner-rotating-plane"></div></div></div>
<div class="row">
	<div class="col-lg-12">
		<blockquote>A break down of the total food produced per state over the course of time as well as the individual components of what are produced</blockquote>
	</div>
</div>
<div class="ibox">
	<div class="ibox-title">
		<h5>General Production</h5>
		<div class="ibox-tools">
			<a class="collapse-link">
				<i class="fa fa-chevron-up"></i>
			</a>
		</div>
	</div>
	<div class="ibox-content">
		<div class="row">
			<div class="col-lg-8">
				<div id="world-map" style="width: 100%; height: 460px"></div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>Year</label>
					<select id="yearSelector" name="yearSelector" class="form-control yearSelectors">
						<option value="">Select a year...</option>
					</select>
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control stateSelectors" id="stateSelector">
						<option value="">Select a state...</option>
						<?php
						$states = ["US-AL" => "Alabama", "US-AK" => "Alaska", "US-AZ" => "Arizona", "US-AR" => "Arkansas", "US-CA" => "California", "US-CO" => "Colorado", "US-CT" => "Connecticut", "US-DE" => "Delaware", "US-FL" => "Florida", "US-GA" => "Georgia", "US-HI" => "Hawaii", "US-ID" => "Idaho", "US-IL" => "Illinois", "US-IN" => "Indiana", "US-IA" => "Iowa", "US-KS" => "Kansas", "US-KY" => "Kentucky", "US-LA" => "Louisiana", "US-ME" => "Maine", "US-MD" => "Maryland", "US-MA" => "Massachusetts", "US-MI" => "Michigan", "US-MN" => "Minnesota", "US-MS" => "Mississippi", "US-MO" => "Missouri", "US-MT" => "Montana", "US-NE" => "Nebraska", "US-NV" => "Nevada", "US-NH" => "New Hampshire", "US-NJ" => "New Jersey", "US-NM" => "New Mexico", "US-NY" => "New York", "US-NC" => "North Carolina", "US-ND" => "North Dakota", "US-OH" => "Ohio", "US-OK" => "Oklahoma", "US-OR" => "Oregon", "US-PA" => "Pennsylvania", "US-RI" => "Rhode Island", "US-SC" => "South Carolina", "US-SD" => "South Dakota", "US-TN" => "Tennessee", "US-TX" => "Texas", "US-UT" => "Utah", "US-VT" => "Vermont", "US-VA" => "Virginia", "US-WA" => "Washington", "US-WV" => "West Virginia", "US-WI" => "Wisconsin", "US-WY" => "Wyoming"];
						foreach ($states as $sK => $sV) {
							echo '<option value="' . $sK . '">' . $sV . '</option>';
						}
						?>
					</select>
				</div>
				<ul class="unstyled stateDetails" style="height: 300px;overflow-y:scroll">
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="ibox" id="productionPerState">
	<div class="ibox-title">
		<h5>Production Per State</h5>
		<div class="ibox-tools">
			<a class="collapse-link">
				<i class="fa fa-chevron-up"></i>
			</a>
		</div>
	</div>
	<div class="ibox-content">
		<div class="row">
			<div class="col-lg-8" id="canvasWrapper">
				<canvas id="lineChart" height="140"></canvas>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>State</label>
					<select class="form-control stateSelectors">
						<option value="">Select a state...</option>
						<?php
						$states = ["US-AL" => "Alabama", "US-AK" => "Alaska", "US-AZ" => "Arizona", "US-AR" => "Arkansas", "US-CA" => "California", "US-CO" => "Colorado", "US-CT" => "Connecticut", "US-DE" => "Delaware", "US-FL" => "Florida", "US-GA" => "Georgia", "US-HI" => "Hawaii", "US-ID" => "Idaho", "US-IL" => "Illinois", "US-IN" => "Indiana", "US-IA" => "Iowa", "US-KS" => "Kansas", "US-KY" => "Kentucky", "US-LA" => "Louisiana", "US-ME" => "Maine", "US-MD" => "Maryland", "US-MA" => "Massachusetts", "US-MI" => "Michigan", "US-MN" => "Minnesota", "US-MS" => "Mississippi", "US-MO" => "Missouri", "US-MT" => "Montana", "US-NE" => "Nebraska", "US-NV" => "Nevada", "US-NH" => "New Hampshire", "US-NJ" => "New Jersey", "US-NM" => "New Mexico", "US-NY" => "New York", "US-NC" => "North Carolina", "US-ND" => "North Dakota", "US-OH" => "Ohio", "US-OK" => "Oklahoma", "US-OR" => "Oregon", "US-PA" => "Pennsylvania", "US-RI" => "Rhode Island", "US-SC" => "South Carolina", "US-SD" => "South Dakota", "US-TN" => "Tennessee", "US-TX" => "Texas", "US-UT" => "Utah", "US-VT" => "Vermont", "US-VA" => "Virginia", "US-WA" => "Washington", "US-WV" => "West Virginia", "US-WI" => "Wisconsin", "US-WY" => "Wyoming"];
						foreach ($states as $sK => $sV) {
							echo '<option value="' . $sK . '">' . $sV . '</option>';
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Crops</label>
					<select class="form-control cropSelection" multiple>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
@stop