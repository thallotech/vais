@extends('global')

@section('title', 'Crowd Sourced - Food Produced')

@section('headerSuffix')
	<!-- Data Tables -->
	<link href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css" rel="stylesheet" />
	<link href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" />
	<link href="//cdn.datatables.net/responsive/1.0.6/css/dataTables.responsive.css" rel="stylesheet" />
	<link href="/js/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
	<style type="text/css">
		.ui-autocomplete {
			position: absolute;
			z-index:9999;
			cursor: default;
			padding: 0;
			margin-top: 2px;
			list-style: none;
			background-color: #ffffff;
			border: 1px solid #ccc
			-webkit-border-radius: 5px;
			   -moz-border-radius: 5px;
				    border-radius: 5px;
			-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
			   -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
				    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}
		.ui-autocomplete > li {
		  padding: 3px 20px;
		}
		.ui-autocomplete > li.ui-state-focus {
		  background-color: #DDD;
		}
		.ui-helper-hidden-accessible {
		  display: none;
		}
		form#addRowForm div.hiddenErrors {
			display:none !important;
		}
	</style>
@stop

<?php
	$produceTypes = \App\CrowdsourcedDataProduceTypes::all();
	$produceUnits = \App\CrowdsourcedDataPrices::distinct()->select('priceUnit')->groupBy('priceUnit')->get();
	$autocompleteData = [];
	foreach ($produceTypes as $k => $value) {
		$autocompleteData['foodItemInput'][] = $value->produceName;
	}
	foreach ($produceUnits as $k => $value) {
		$autocompleteData['foodUnitInput'][] = $value->priceUnit;
	}
?>

@section('footerSuffix')
<!-- jQuery UI -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>
	<!-- DataTables -->
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>
	<script src="//cdn.datatables.net/responsive/1.0.6/js/dataTables.responsive.min.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-us-mill-en.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="/js/plugins/jvectormap/jquery-jvectormap-us-lcc-en.js"></script>
	
	<script src="//cdn.jsdelivr.net/jquery.validation/1.14.0/jquery.validate.min.js"></script>
	<script src="//cdn.jsdelivr.net/jquery.validation/1.14.0/additional-methods.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript">
	<!--
		jQuery(document).on('ready', function() {
			var jsonData = <?php $c = new \App\Http\Controllers\DataProcessingController; echo $c->getAllCSDPricing() ?>;
			var autocompleteData = <?php echo json_encode($autocompleteData); ?>;
			jQuery("#cropSelector").val("");
				var cropValue = jQuery("#cropSelector").val();
				var yearValue = jQuery("#yearSelector").val();
				var mapObject = new jvm.MultiMap({
					container: $('#world-map'),
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
								max: "10.00"
							}]
						},
					  map: 'us_lcc_en',
					  onRegionTipShow: function(event, label, code){
						  if (cropValue == "") {
							label.html(
							  '<b>'+label.html()+'</b></br>'+
							  '<b>Select a crop to load data</b>'
							);
						  }
						  else {
							label.html(
							  '<b>'+label.html()+'</b></br>$'+
							  jsonData['crops'][yearValue][cropValue][code] + " / " + jsonData['units'][cropValue]
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
				jQuery("body").on('change', "#cropSelector", function(e) {
					e.preventDefault();
					var val = jQuery(this).val();
					cropValue = val;
					var year = jQuery("#yearSelector").val();
					yearValue = year;
					mapLoadedObj.series.regions[0].setValues(jsonData['crops'][year][val]);
				});
				jQuery.each(autocompleteData, function (k, v) {
					jQuery("input#" + k).autocomplete({ source: v });
				});
				jQuery("button.addRow").on('click', function(e) {
					e.preventDefault();
					jQuery("#addRowForm input.autocomplete").removeAttr('autocomplete');
					jQuery("#addRowBox").removeClass('hidden');
				});
				jQuery.validator.addMethod(
					"money",
					function(value, element) {
						var isValidMoney = /^\d{0,4}(\.\d{0,2})?$/.test(value);
						return this.optional(element) || isValidMoney;
					}
				);
				var addRowForm = jQuery("#addRowForm").validate({
								debug: true,
								errorClass: 'error',
								validClass: 'success',
								errorPlacement: function (error, element) { },
								errorLabelContainer: jQuery("#addRowForm div.hiddenErrors"),
								ignore: [],
								highlight: function(element, errorClass, validClass) {
									if (jQuery(element).hasClass('unitSizeSelection')) {
										jQuery(element).prev().prev().addClass(errorClass).removeClass(validClass);
									}
									else {
										jQuery(element).addClass(errorClass).removeClass(validClass);
										jQuery('label[for="' + jQuery(element).attr('id') + '"]').addClass(errorClass).removeClass(validClass);
									}
								},
								unhighlight: function(element, errorClass, validClass) {
									if (jQuery(element).hasClass('unitSizeSelection')) {
										jQuery(element).prev().prev().addClass(validClass).removeClass(errorClass);
									}
									else {
										jQuery(element).addClass(validClass).removeClass(errorClass);
										jQuery('label[for="' + jQuery(element).attr('id') + '"]').addClass(validClass).removeClass(errorClass);
									}
								},
								invalidHandler: function(event, validator) {
									// 'this' refers to the form
									var errors = validator.numberOfInvalids();
									if (errors) {
										var message = errors == 1
										? 'You missed 1 field. It has been highlighted'
										: 'You missed ' + errors + ' fields. They have been highlighted';
										jQuery("#addRowBox .ibox-title span.label-danger").html(message);
										jQuery("#addRowBox button.btn-primary").removeClass('disabled');
									} else {
										jQuery("#addRowBox .ibox-title span.label-danger").html('');
									}
								},
								rules: {
									foodPriceInput: {
										money: true,
										required: true
									}
								},
								submitHandler: function(form, event) {
									event.preventDefault();
									
									jQuery("#addRowBox button.btn-primary").addClass('disabled');
									jQuery("#addRowBox .ibox-title span.label").text('');
									jQuery.ajax({
										url: '/ajax/cs/food-prices',
										method: 'POST',
										data: jQuery("#addRowForm").serialize(),
										success: function(result) {
											switch (result) {
												case "SUCCESS":
													jQuery("#addRowForm input.form-control").val('');
													jQuery("#addRowBox .ibox-title span.label-success").text("Successfully submitted");
													setTimeout(function() { jQuery("#addRowBox .ibox-title span.label-success").fadeOut('fast'); }, 2000);
													jQuery("#addRowBox button.btn-primary").removeClass('disabled');
												break;
												default:
													jQuery("#addRowBox .ibox-title span.label-warning").text("Submission Error");
													jQuery("#addRowBox button.btn-primary").removeClass('disabled');
												break;
											}
										}
									});
								}
							});
		});
	//-->
	</script>
@stop

@section('content')
<div class="row">
	<div class="col-lg-10">
		<blockquote>Here you can see the average cost of food around the United States as well as add your own data to show what prices are like in your town</blockquote>
	</div>
	<div class="col-lg-2">
		<button class="pull-right btn btn-primary addRow">Add Pricing Data</button>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="ibox hidden" id="addRowBox">
			<div class="ibox-title">
				<span class="label label-danger pull-right"></span>
				<span class="label label-success pull-right"></span>
				<h5>Add Pricing Data</h5>
			</div>
			<div class="ibox-content">
				<form id="addRowForm" class="row" role="form">
					<div class="form-group col-lg-2">
						<label for="foodItemInput">Food Item</label>
						<input required type="text" class="form-control autocomplete ui-autocomplete-input" id="foodItemInput" name="foodItemInput" placeholder="Food Item" autocomplete="off" />
					</div>
					<div class="form-group col-lg-1">
						<label for="foodPriceInput">Price</label>
						<input required type="text" class="form-control" id="foodPriceInput" name="foodPriceInput" placeholder="xx.xx" autocomplete="off" />
					</div>
					<div class="form-group col-lg-2">
						<label for="foodUnitInput">Per</label>
						<input required type="text" class="form-control autocomplete ui-autocomplete-input" id="foodUnitInput" name="foodUnitInput" placeholder="Unit" autocomplete="off" />
					</div>
					<div class="form-group col-lg-1">
						<label for="foodZipcodeInput">Zip Code</label>
						<input required type="text" minlength="5" maxlength="5" class="form-control" id="foodZipcodeInput" name="foodZipcodeInput" placeholder="90210" autocomplete="off" />
					</div>
					<div class="form-group col-lg-2">
						<label for="foodZipcodeInput">Email</label>
						<input required type="email" class="form-control" id="foodEmailInput" name="foodEmailInput" placeholder="email@example.com" autocomplete="off" />
					</div>
					<div class="form-group col-lg-2">
						<label for="foodSource">Source</label>
						<input required type="text" class="form-control" id="foodSource" name="foodSource" placeholder="eg Krogers" autocomplete="off" />
					</div>
					{!! csrf_field() !!}
					<div class="form-group col-lg-1">
						<label>&nbsp;</label>
						<button class="btn btn-primary">Submit</button>
					</div>
					<div class="hiddenErrors">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="ibox">
	<div class="ibox-content">
		<div class="row">
			<div class="col-lg-8">
				<div id="world-map" style="width: 100%; height: 460px">
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>Year</label>
					<select class="form-control" id="yearSelector">
						<option value="2015">2015</option>
					</select>
				</div>
				<div class="form-group">
					<label>Crop</label>
					<select class="form-control" id="cropSelector">
						<option value="" selected="selected">Select a crop</option>
						<?php
							$crops = \App\CrowdsourcedDataProduceTypes::all();
							foreach ($crops as $crop => $value) {
								echo '<option value="' .  $value->slug . '">' . $value->produceName . '</option>';
							}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="tester"></div>
@stop