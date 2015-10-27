@extends('global')

@section('title', 'Time Lapse - Weather Effects')

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
	
    <!-- ChartJS-->
    <script src="/js/plugins/chartJs/Chart.min.js"></script>
	<script type="text/javascript">
	<!--
		jQuery(document).on('ready', function() {
			var jsonData = jQuery.ajax({
				url: "/ajax/time/getWeatherData",
				method: "GET",
				success: function(result) {
					//console.log(result);
					var dataReturned = JSON.parse(result);
					jsonData = dataReturned;
					var years = commodities = [];
					jQuery.each(dataReturned['data'], function(index, value) {
						years.push(index);
					});
					years.sort(function(a, b){return b-a});
					jQuery.each(years, function(index, value) {
						jQuery("#yearSelector").append('<option value="' + value + '">' + value + '</option>');
						if (value > "1971") {
							jQuery("#climatePerState .yearSelectors").append('<option value="' + value + '">' + value + '</option>');
						}
					});
					jQuery.each(dataReturned['commodities'], function(index, value) {
						jQuery(".commoditySelectors").append('<option value="' + value + '">' + value + '</option>');
					});
					jQuery("#commoditySelector").val("");
					jQuery("#loading_wrap").fadeOut('fast');
					return dataReturned;
				}
			});
			var yearValue = jQuery("#yearSelector").val();
			var monthValue = jQuery("#monthSelector").val();
			var commodityValue = jQuery("#commoditySelector").val();
			var climateYearValue = jQuery("#climatePerState .yearSelectors").val();
			var climateStateValue = jQuery("#climatePerState .stateSelectors").val();
			var climateCommodityValue = jQuery("#climatePerState .commoditySelectors").val();
			var mapObject = new jvm.MultiMap({
				container: jQuery('#world-map'),
				maxLevel: 1,
				main: {
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
							min: "0.00",
							max: "20.00"
						}]
					},
				  map: 'us_lcc_en',
				  onRegionTipShow: function(event, label, code){
					  if (commodityValue == "") {
						label.html(
						  '<b>'+label.html()+'</b></br>'+
						  '<b>Select a commodity to load data</b>'
						);
					  }
					  else {
						  /*if (jsonData['data'][yearValue][monthValue][commodityValue] === undefined) {
							  label.html(
							  '<b>'+label.html()+'</b></br>No data'
							);
						  }
						  else {*/
							label.html(
							  '<b>'+label.html()+'</b></br>$'+
							  jsonData['data'][yearValue][monthValue][commodityValue][code] + "/cwt"
							);
						  //}
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
			jQuery("body").on('change', "#commoditySelector, #yearSelector, #monthSelector", function(e) {
				e.preventDefault();
				commodityValue = jQuery("#commoditySelector").val();
				yearValue = jQuery("#yearSelector").val();
				monthValue = jQuery("#monthSelector").val();
				mapLoadedObj.series.regions[0].setValues(jsonData['data'][yearValue][monthValue][commodityValue]);
				//console.log(jsonData['data'][yearValue][monthValue][commodityValue]);
			});
			var lineData = {
				labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
				datasets: [
					{
						label: "Price",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [0,0,0,0,0,0,0,0,0,0,0,0]
					},
					{
						label: "Temp (High)",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [0,0,0,0,0,0,0,0,0,0,0,0]
					},
					{
						label: "Temp (Low)",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [0,0,0,0,0,0,0,0,0,0,0,0]
					},
					{
						label: "Precipitation",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [0,0,0,0,0,0,0,0,0,0,0,0]
					}
				]
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
				showScale: false,
				responsive: true,
			};

			var ctx = document.getElementById("lineChart").getContext("2d");
			var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
			
			jQuery("#climatePerState").on('change', 'select', function(e) {
				e.preventDefault();
				climateCommodityValue = jQuery("#climatePerState .commoditySelectors").val();
				climateYearValue = jQuery("#climatePerState .yearSelectors").val();
				climateStateValue = jQuery("#climatePerState .stateSelectors").val();
				if (climateCommodityValue != "") {
					jQuery.ajax({
						method: "GET",
						url: '/ajax/time/processWeatherData/' + climateYearValue + '/' + climateStateValue,
						success: function(result) {
							//console.log(result);
							var dat = JSON.parse(result);
							jQuery.each(dat, function(index, value) {
								//console.log(index);
								var counter = 0;
								switch(index) {
									case "tempHigh":
										jQuery.each(value, function(indx, valu) {
											myNewChart.datasets[1].points[counter].value = valu;
											counter++;
										});
									break;
									case "tempLow":
										jQuery.each(value, function(indx, valu) {
											myNewChart.datasets[2].points[counter].value = valu;
											counter++;
										});
									break;
									case "precip":
										jQuery.each(value, function(indx, valu) {
											myNewChart.datasets[3].points[counter].value = valu;
											counter++;
										});
									break;
								}
							});
							var months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
							jQuery.each(months, function(index, value) {
								myNewChart.datasets[0].points[index].value = jsonData['data'][climateYearValue][value][climateCommodityValue][climateStateValue];
							});
							myNewChart.update();
						}
					});
				}
			});
		});
	//-->
	</script>
@stop


@section('content')
<div id='loading_wrap' style='position:fixed; height:100%; width:100%; overflow:hidden; top:0; left:0;z-index:9999; background:rgba(255,255,255,0.85)'><div class="spiner-example"><div class="sk-spinner sk-spinner-rotating-plane"></div></div></div>
<div class="row">
	<div class="col-lg-12">
		<blockquote>Here you can see the price of food per state and how climate change effects prices</blockquote>
	</div>
</div>
<div class="ibox">
	<div class="ibox-title">
		<h5>General Price Breakdown</h5>
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
					</select>
				</div>
				<div class="form-group">
					<label>Month</label>
					<select id="monthSelector" name="monthSelector" class="form-control">
						<option value="JAN">January</option>
						<option value="FEB">February</option>
						<option value="MAR">March</option>
						<option value="APR">April</option>
						<option value="MAY">May</option>
						<option value="JUN">June</option>
						<option value="JUL">July</option>
						<option value="AUG">August</option>
						<option value="SEP">September</option>
						<option value="OCT">October</option>
						<option value="NOV">November</option>
						<option value="DEC">December</option>
					</select>
				</div>
				<div class="form-group">
					<label>Commodity</label>
					<select id="commoditySelector" name="commoditySelector" class="form-control commoditySelectors">
						<option value="">Select a commodity...</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="ibox" id="climatePerState">
	<div class="ibox-title">
		<h5>Climate/Price Per State</h5>
		<div class="ibox-tools">
			<a class="collapse-link">
				<i class="fa fa-chevron-up"></i>
			</a>
		</div>
	</div>
	<div class="ibox-content">
		<div class="row">
			<div class="col-lg-8">
				<canvas id="lineChart" height="140"></canvas>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>Year</label>
					<select class="form-control yearSelectors">
					</select>
				</div>
				<div class="form-group">
					<label>State</label>
					<select class="form-control stateSelectors">
						<?php
						$states = ["US-AL" => "Alabama", "US-AK" => "Alaska", "US-AZ" => "Arizona", "US-AR" => "Arkansas", "US-CA" => "California", "US-CO" => "Colorado", "US-CT" => "Connecticut", "US-DE" => "Delaware", "US-FL" => "Florida", "US-GA" => "Georgia", "US-HI" => "Hawaii", "US-ID" => "Idaho", "US-IL" => "Illinois", "US-IN" => "Indiana", "US-IA" => "Iowa", "US-KS" => "Kansas", "US-KY" => "Kentucky", "US-LA" => "Louisiana", "US-ME" => "Maine", "US-MD" => "Maryland", "US-MA" => "Massachusetts", "US-MI" => "Michigan", "US-MN" => "Minnesota", "US-MS" => "Mississippi", "US-MO" => "Missouri", "US-MT" => "Montana", "US-NE" => "Nebraska", "US-NV" => "Nevada", "US-NH" => "New Hampshire", "US-NJ" => "New Jersey", "US-NM" => "New Mexico", "US-NY" => "New York", "US-NC" => "North Carolina", "US-ND" => "North Dakota", "US-OH" => "Ohio", "US-OK" => "Oklahoma", "US-OR" => "Oregon", "US-PA" => "Pennsylvania", "US-RI" => "Rhode Island", "US-SC" => "South Carolina", "US-SD" => "South Dakota", "US-TN" => "Tennessee", "US-TX" => "Texas", "US-UT" => "Utah", "US-VT" => "Vermont", "US-VA" => "Virginia", "US-WA" => "Washington", "US-WV" => "West Virginia", "US-WI" => "Wisconsin", "US-WY" => "Wyoming"];
						foreach ($states as $sK => $sV) {
							echo '<option value="' . $sK . '">' . $sV . '</option>';
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Commodity</label>
					<select class="form-control commoditySelectors">
						<option value="">Select a commodity...</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
@stop