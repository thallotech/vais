@extends('global')

@section('title', 'Home')

@section('headerSuffix')
@stop

@section('footerSuffix')
	<!-- jQuery UI -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>
	<script type="text/javascript">
	<!--
		jQuery(document).on('ready', function() {
			
		});
	//-->
	</script>
@stop


@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="ibox">
			<div class="ibox-content text-center p-md">
				<h2><span class="text-navy">VAIS - Visualized Agriculture Information System</span><br />Helping explain the history and future of the United States food market</h2>
				<p>Our interactive application incorporates weather information, production data from the NASS database, and crowd-sourced pricing data to create a meaningful picture of the United States food market. VAIS provides easily accessible, time-series, nation-wide data to help researchers address the vulnerability of the food system by visualizing how the nation’s food supply and production are changing as a direct result of climate change, consumer demand, and productivity. Utilizing our application, agricultural workers will make more informed decisions regarding the production and distribution of produce across the United States. Integrating data from multiple sources is vital to the resiliency of America’s food supply and our application is a great way to begin addressing the challenges ahead of us.</p>
			</div>
		</div>
	</div>
</div>


@stop