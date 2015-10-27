<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Validator;

class DataProcessingController extends Controller
{
	public function getFoodProductionData() {
		$returnData = ['income' => [], 'production' => [], 'totals' => [], 'commodities' => [], 'cropJoints' => []];
		//$nassData = "http://nass-api.azurewebsites.net/api/api_get?freq_desc=ANNUAL&agg_level_desc=STATE&source_desc=SURVEY&sector_desc=CROPS&group_desc=VEGETABLES&commodity_desc__or=ARTICHOKES&commodity_desc__or=ASPARAGUS&commodity_desc__or=BEANS&commodity_desc__or=BEETS&commodity_desc__or=BROCCOLI&commodity_desc__or=BRUSSELS%20SPROUTS&commodity_desc__or=CABBAGE&commodity_desc__or=CARROTS&commodity_desc__or=CAULIFLOWER&commodity_desc__or=CELERY&commodity_desc__or=CUCUMBERS&commodity_desc__or=EGGPLANT&commodity_desc__or=ESCAROLE%20%26%20ENDIVE&commodity_desc__or=GARLIC&commodity_desc__or=GINGER%20ROOT&commodity_desc__or=GREENS&commodity_desc__or=LETTUCE&commodity_desc__or=MELONS&commodity_desc__or=OKRA&commodity_desc__or=ONIONS&commodity_desc__or=PEAS&commodity_desc__or=PEPPERS&commodity_desc__or=POTATOES&commodity_desc__or=PUMPKINS&commodity_desc__or=RADISHES&commodity_desc__or=SPINACH&commodity_desc__or=SQUASH&commodity_desc__or=SWEET%20CORN&commodity_desc__or=SWEET%20POTATOES&commodity_desc__or=TOMATOES&commodity_desc__or=VEGETABLE%20TOTALS&statisticcat_desc=YIELD";
		//$nassData = json_decode(file_get_contents("http://nass-api.azurewebsites.net/api/api_get?agg_level_desc=STATE&source_desc=SURVEY&sector_desc=CROPS&group_desc=VEGETABLES&commodity_desc__or=ARTICHOKES&commodity_desc__or=ASPARAGUS&commodity_desc__or=BEANS&commodity_desc__or=BEETS&commodity_desc__or=BROCCOLI&commodity_desc__or=BRUSSELS%20SPROUTS&commodity_desc__or=CABBAGE&commodity_desc__or=CARROTS&commodity_desc__or=CAULIFLOWER&commodity_desc__or=CELERY&commodity_desc__or=CUCUMBERS&commodity_desc__or=EGGPLANT&commodity_desc__or=ESCAROLE%20%26%20ENDIVE&commodity_desc__or=GARLIC&commodity_desc__or=GINGER%20ROOT&commodity_desc__or=GREENS&commodity_desc__or=LETTUCE&commodity_desc__or=MELONS&commodity_desc__or=OKRA&commodity_desc__or=ONIONS&commodity_desc__or=PEAS&commodity_desc__or=PEPPERS&commodity_desc__or=POTATOES&commodity_desc__or=PUMPKINS&commodity_desc__or=RADISHES&commodity_desc__or=SPINACH&commodity_desc__or=SQUASH&commodity_desc__or=SWEET%20CORN&commodity_desc__or=SWEET%20POTATOES&commodity_desc__or=TOMATOES&commodity_desc__or=VEGETABLE%20TOTALS&statisticcat_desc=PRODUCTION&year__or=2014&year__or=2013&year__or=2012&year__or=2011&year__or=2010&year__or=2009&year__or=2008&year__or=2007"), true);
		$nassData = json_decode(file_get_contents("http://nass-api.azurewebsites.net/api/api_get?agg_level_desc=STATE&source_desc=SURVEY&sector_desc=CROPS&group_desc=VEGETABLES&year__or=2014&year__or=2013&year__or=2012&year__or=2011&year__or=2010&year__or=2009&year__or=2008&year__or=2007&commodity_desc__or=ARTICHOKES&commodity_desc__or=ASPARAGUS&commodity_desc__or=BEANS&commodity_desc__or=BEETS&commodity_desc__or=BROCCOLI&commodity_desc__or=BRUSSELS%20SPROUTS&commodity_desc__or=CABBAGE&commodity_desc__or=CARROTS&commodity_desc__or=CAULIFLOWER&commodity_desc__or=CELERY&commodity_desc__or=CUCUMBERS&commodity_desc__or=EGGPLANT&commodity_desc__or=ESCAROLE%20%26%20ENDIVE&commodity_desc__or=GARLIC&commodity_desc__or=GINGER%20ROOT&commodity_desc__or=GREENS&commodity_desc__or=LETTUCE&commodity_desc__or=MELONS&commodity_desc__or=OKRA&commodity_desc__or=ONIONS&commodity_desc__or=PEAS&commodity_desc__or=PEPPERS&commodity_desc__or=POTATOES&commodity_desc__or=PUMPKINS&commodity_desc__or=RADISHES&commodity_desc__or=SPINACH&commodity_desc__or=SQUASH&commodity_desc__or=SWEET%20CORN&commodity_desc__or=SWEET%20POTATOES&commodity_desc__or=TOMATOES&statisticcat_desc=YIELD&freq_desc=ANNUAL"),true);
		$nassPriceData = json_decode(file_get_contents(storage_path() . '/productionTotalIncome.json'), true);
		$nassAcreageData = json_decode(file_get_contents(storage_path() . '/productionAcres.json'), true);
		$years = ["2007", "2008", "2009", "2010", "2011", "2012", "2013", "2014"];
		$states = ["US-AL", "US-AK", "US-AZ", "US-AR", "US-CA", "US-CO", "US-CT", "US-DE", "US-FL", "US-GA", "US-HI", "US-ID", "US-IL", "US-IN", "US-IA", "US-KS", "US-KY", "US-LA", "US-ME", "US-MD", "US-MA", "US-MI", "US-MN", "US-MS", "US-MO", "US-MT", "US-NE", "US-NV", "US-NH", "US-NJ", "US-NM", "US-NY", "US-NC", "US-ND", "US-OH", "US-OK", "US-OR", "US-PA", "US-RI", "US-SC", "US-SD", "US-TN", "US-TX", "US-UT", "US-VT", "US-VA", "US-WA", "US-WV", "US-WI", "US-WY"];
		foreach ($years as $yK => $yV) {
			foreach ($states as $sK => $sV) {
				$returnData['income'][$yV][$sV] = "0";
				$returnData['totals'][$yV][$sV] = "0";
			}
		}
		foreach ($nassPriceData['data'] as $pK => $pV) {
			$returnData['income'][$pV['year']]["US-" . $pV['state_alpha']] = $pV['value'];
		}
		foreach ($nassData['data'] as $nK => $nV) {
			//rD['production']['year]['state'][com]
			switch (trim($nV['unit_desc'])) {
				case "CWT / ACRE":
					$compVal = ($nV['value'] * 100);
				break;
				case "TONS / ACRE":
					$compVal = ($nV['value'] * 2000);
				break;
				default: //lbs per acre
					$compVal = $nV['value'];
				break;
			}
			if ($nV['state_alpha'] != "OT") { //wtfhax what the deuce is OT?
				$returnData['production'][$nV['year']]["US-" . $nV['state_alpha']][$nV['commodity_desc']][$nV['class_desc']] = ['produced' => $compVal, 'acres' => ''];
				$returnData['totals'][$nV['year']]["US-" . $nV['state_alpha']] = ($returnData['totals'][$nV['year']]["US-" . $nV['state_alpha']] + $compVal);
				$returnData['commodities'][$nV['commodity_desc']][$nV['class_desc']] = '';
				$returnData['cropJoints']["US-" . $nV['state_alpha']][$nV['commodity_desc'] . ' - ' . $nV['class_desc']][$nV['year']] = $compVal;
			}
		}
		
		ksort($returnData['cropJoints']);
		
		foreach ($nassAcreageData['data'] as $aK => $aV) {
			if (is_numeric(str_replace(',', '', $aV['value']))) {
				if ($aV['state_alpha'] != "OT") {
					$returnData['production'][$aV['year']]["US-" . $aV['state_alpha']][$aV['commodity_desc']][$aV['class_desc']]['acres'] = str_replace(',', '', $aV['value']);
				}
			}
		}
		return json_encode($returnData);
	}
	
	public function processWeatherData($year, $state) {
		$stateJSON = json_decode('{"US-AL":{"lat":"32.3737981","long":"-86.3110071"},"US-AK":{"lat":"58.3018264","long":"-134.4202956"},"US-AZ":{"lat":"33.4500000","long":"-112.0700000"},"US-AR":{"lat":"34.7499657","long":"-92.2852014"},"US-CA":{"lat":"38.5800000","long":"-121.4900000"},"US-CO":{"lat":"39.7500000","long":"-104.9900000"},"US-CT":{"lat":"41.7899681","long":"-72.6622361"},"US-DE":{"lat":"39.2081349","long":"-75.4577775"},"US-FL":{"lat":"30.4305062","long":"-84.2542195"},"US-GA":{"lat":"33.7600000","long":"-84.3900000"},"US-HI":{"lat":"21.3100000","long":"-157.8600000"},"US-ID":{"lat":"43.6136565","long":"-116.2173542"},"US-IL":{"lat":"39.7994917","long":"-89.6498742"},"US-IN":{"lat":"39.6521264","long":"-86.2815581"},"US-IA":{"lat":"41.6000000","long":"-93.6100000"},"US-KS":{"lat":"39.0541716","long":"-95.6721428"},"US-KY":{"lat":"38.2481018","long":"-84.8984775"},"US-LA":{"lat":"30.4483779","long":"-91.1887080"},"US-ME":{"lat":"44.3520015","long":"-69.6923760"},"US-MD":{"lat":"38.9794340","long":"-76.4919706"},"US-MA":{"lat":"42.3571672","long":"-71.0567853"},"US-MI":{"lat":"42.7327000","long":"-84.5557199"},"US-MN":{"lat":"44.9513188","long":"-93.0901841"},"US-MS":{"lat":"32.2907734","long":"-90.1846217"},"US-MO":{"lat":"38.5281070","long":"-92.1646722"},"US-MT":{"lat":"46.5570751","long":"-112.2285158"},"US-NE":{"lat":"40.8185796","long":"-96.7112722"},"US-NV":{"lat":"39.1297282","long":"-119.6730939"},"US-NH":{"lat":"43.2086343","long":"-71.5486562"},"US-NJ":{"lat":"40.2200000","long":"-74.7400000"},"US-NM":{"lat":"35.7135437","long":"-105.8407722"},"US-NY":{"lat":"42.6500000","long":"-73.7600000"},"US-NC":{"lat":"35.7744301","long":"-78.6313624"},"US-ND":{"lat":"46.8243438","long":"-100.6597530"},"US-OH":{"lat":"40.1038320","long":"-83.0200245"},"US-OK":{"lat":"35.3971124","long":"-97.6519288"},"US-OR":{"lat":"44.9464822","long":"-123.0018941"},"US-PA":{"lat":"40.2584515","long":"-76.8865085"},"US-RI":{"lat":"41.8300000","long":"-71.4100000"},"US-SC":{"lat":"34.0909782","long":"-80.9657825"},"US-SD":{"lat":"44.3683222","long":"-100.3510108"},"US-TN":{"lat":"36.0600000","long":"-86.6700000"},"US-TX":{"lat":"30.2228447","long":"-97.7473572"},"US-UT":{"lat":"40.7563925","long":"-111.8985922"},"US-VT":{"lat":"44.2606400","long":"-72.5778000"},"US-VA":{"lat":"37.5745428","long":"-77.5433122"},"US-WA":{"lat":"46.9770179","long":"-122.8584190"},"US-WV":{"lat":"38.3517112","long":"-81.6336474"},"US-WI":{"lat":"43.0700000","long":"-89.3800000"},"US-WY":{"lat":"41.1400096","long":"-104.8201078"}}', true);
		$long = $stateJSON[$state]['long'];
		$lat = $stateJSON[$state]['lat'];
		$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		$days = ["01", "07", "14", "21"];
		$dateStrings = [];
		foreach ($months as $mK => $mV) {
			foreach ($days as $dK => $dV) {
				$timeset = strtotime($dV . " " . $mV . " " . $year);
				if ($timeset < time()) {
					$dateStrings[] = $timeset;
				}
			}
		}
		$returnData = $tempHigh = $tempLow = $precip = [];
		foreach ($dateStrings as $dK => $dV) {
			$db = \App\WeatherData::where(['time' => $dV, 'state' => str_replace('US-', '', $state)])->first();
			$month = date("M", $dV);
			if (count($db) > 0) {
				$decoded = json_decode($db->data, true);
				$tempHigh[$month][] = $decoded['daily']['data'][0]['temperatureMax'];
				$tempLow[$month][] = $decoded['daily']['data'][0]['temperatureMin'];
				$precip[$month][] = $decoded['daily']['data'][0]['precipIntensity'];
			}
			else {
				$darkSky = file_get_contents("https://api.forecast.io/forecast/" . env('DARKSKY_API_KEY', '') . "/" . $lat . "," . $long . "," . $dV);
				$insert = new \App\WeatherData;
				$insert->time = $dV;
				$insert->state = str_replace('US-', '', $state);
				$insert->data = $darkSky;
				$insert->save();
				$decoded = json_decode($darkSky, true);
				$tempHigh[$month][] = $decoded['daily']['data'][0]['temperatureMax'];
				$tempLow[$month][] = $decoded['daily']['data'][0]['temperatureMin'];
				$precip[$month][] = $decoded['daily']['data'][0]['precipIntensity'];
			}
		}
		foreach ($tempHigh as $m => $v) {
			$returnData['tempHigh'][$m] = array_sum($v) / count($v);
		}
		foreach ($tempLow as $m => $v) {
			$returnData['tempLow'][$m] = array_sum($v) / count($v);
		}
		foreach ($precip as $m => $v) {
			$returnData['precip'][$m] = array_sum($v);
		}
		return json_encode($returnData);
	}
	public function getWeatherData() {
		//$nassData = json_decode(file_get_contents("http://nass-api.azurewebsites.net/api/api_get?source_desc=SURVEY&freq_desc=MONTHLY&sector_desc=CROPS&group_desc=VEGETABLES&commodity_desc__or=ARTICHOKES&commodity_desc__or=ASPARAGUS&commodity_desc__or=BEANS&commodity_desc__or=BEETS&commodity_desc__or=BROCCOLI&commodity_desc__or=CABBAGE&commodity_desc__or=CARROTS&commodity_desc__or=CAULIFLOWER&commodity_desc__or=CELERY&commodity_desc__or=CUCUMBERS&commodity_desc__or=EGGPLANT&commodity_desc__or=ESCAROLE%20%26%20ENDIVE&commodity_desc__or=GARLIC&commodity_desc__or=GINGER%20ROOT&commodity_desc__or=LETTUCE&commodity_desc__or=MELONS&commodity_desc__or=ONIONS&commodity_desc__or=PEAS&commodity_desc__or=PEPPERS&commodity_desc__or=POTATOES&commodity_desc__or=SPINACH&commodity_desc__or=SWEET%20CORN&commodity_desc__or=SWEET%20POTATOES&commodity_desc__or=TOMATOES&commodity_desc__or=VEGETABLE%20TOTALS&statisticcat_desc=PRICE%20RECEIVED&agg_level_desc=STATE"), true);
		//$formattedData = json_decode(file_get_contents(storage_path() . '/cropPricingData.json'), true);
		$formattedData = file_get_contents(storage_path() . '/cropPricingData.json');
		/*$formattedData = ['data' => [], 'commodities' => []];
		$years = ["2015", "2014", "2013", "2012", "2011", "2010", "2009", "2008", "2007", "2006", "2005", "2004", "2003", "2002", "2001", "2000", "1999", "1998", "1997", "1996", "1995", "1994", "1993", "1992", "1991", "1990", "1989", "1988", "1987", "1986", "1985", "1984", "1983", "1982", "1981", "1980", "1979", "1978", "1977", "1976", "1975", "1974", "1973", "1972", "1971", "1970", "1969", "1968", "1967", "1966", "1965", "1964", "1963", "1962", "1961", "1960", "1959", "1958", "1957", "1956", "1955", "1954", "1953", "1952", "1951", "1950", "1949", "1948", "1947", "1946", "1945", "1944", "1943", "1942", "1941", "1940", "1939", "1938", "1937", "1936", "1935", "1934", "1933", "1932", "1931", "1930", "1929", "1928", "1927", "1926", "1925", "1924", "1923", "1922", "1921", "1920", "1919", "1918", "1917", "1916", "1915", "1914", "1913", "1912", "1911", "1910", "1909"];
		$states = ["US-AL", "US-AK", "US-AZ", "US-AR", "US-CA", "US-CO", "US-CT", "US-DE", "US-FL", "US-GA", "US-HI", "US-ID", "US-IL", "US-IN", "US-IA", "US-KS", "US-KY", "US-LA", "US-ME", "US-MD", "US-MA", "US-MI", "US-MN", "US-MS", "US-MO", "US-MT", "US-NE", "US-NV", "US-NH", "US-NJ", "US-NM", "US-NY", "US-NC", "US-ND", "US-OH", "US-OK", "US-OR", "US-PA", "US-RI", "US-SC", "US-SD", "US-TN", "US-TX", "US-UT", "US-VT", "US-VA", "US-WA", "US-WV", "US-WI", "US-WY"];
		$months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
		$commodities = ["POTATOES","CUCUMBERS","ONIONS","CARROTS","BROCCOLI","SWEET CORN","CAULIFLOWER","CELERY","ASPARAGUS","BEANS","TOMATOES","LETTUCE","MELONS","SPINACH","CABBAGE"];
		foreach ($years as $year) {
			foreach ($months as $mo) {
				foreach ($commodities as $com) {
					foreach ($states as $state) {
						$formattedData['data'][$year][$mo][$com][$state] = "0";
					}
				}
			}
		}
		foreach ($nassData['data'] as $item => $obj) {
			//$fD[year][month][commodity][state]
			if (is_numeric($obj['value'])) {
				$formattedData['data'][$obj['year']][$obj['reference_period_desc']][$obj['commodity_desc']]["US-" . $obj['state_alpha']] = $obj['value'];
				if (!in_array($obj['commodity_desc'], $formattedData['commodities'])) { $formattedData['commodities'][] = $obj['commodity_desc']; }
				if (!in_array($obj['state_alpha'], $states)) { $states[] = $obj['state_alpha']; }
			}
		}
		$nassData = null;*/
		//return json_encode($formattedData);
		return $formattedData;
	}
	
	public function addCSDPricingRow(Request $request) {
		if (\Request::isMethod('post') && \Request::ajax()) {
			$data = $request->all();
			$validator = Validator::make($request->all(), [
				'foodPriceInput' => 'required|max:255|regex:/^\d{0,4}(\.\d{0,2})?$/',
				'foodUnitInput' => 'required|max:255',
				'foodItemInput' => 'required|max:255',
				'foodZipcodeInput' =>'required|min:5|max:5',
				'foodEmailInput' => 'required|email',
				'foodSource' => 'required|max:255'
			]);
			if ($validator->fails()) {
				return "ERROR";
			}
			else {
				$zipData = json_decode(file_get_contents("http://api.zippopotam.us/us/" . $data['foodZipcodeInput']), true);
				
				$produceType = \App\CrowdsourcedDataProduceTypes::firstOrCreate(['produceName' => $data['foodItemInput']]);
				$produceSource = \App\CrowdsourcedDataPriceSources::firstOrCreate(['source' => $data['foodSource']]);
				$price = new \App\CrowdsourcedDataPrices;
				$price->email = $data['foodEmailInput'];
				$price->zipCode = $data['foodZipcodeInput'];
				$price->price = $data['foodPriceInput'];
				$price->priceUnit = $data['foodUnitInput'];
				$price->stateAbrv = $zipData['places'][0]['state abbreviation'];
				$price->save();
				$price->crowdsourcedDataProduceTypes()->attach($produceType);
				$price->crowdsourcedDataPriceSources()->attach($produceSource);
				return "SUCCESS";
			}
		}
	}
	
	public function getAllCSDPricing() {
		$returnArr = ["crops" => []];
		$slugObj = \App\CrowdsourcedDataProduceTypes::all();
		foreach ($slugObj as $cK => $cV) {
			$corr = DB::table('csdPrice_csdProduceType')->where('produceType_id', $cV->id)->get();
			$priceIDs = [];
			foreach ($corr as $k => $v) {
				$priceIDs[] = $v->price_id;
			}
			$prices = DB::table('csdPrices')->select(DB::raw('YEAR(created_at) AS year, priceUnit, stateAbrv, AVG(price) AS price'))->whereIn('id', $priceIDs)->groupBy('stateAbrv')->get();
			foreach ($prices as $pK => $pV) {
				//$yearArr = date('Y', $pV->created_at);
				$year = $pV->year;
				$returnArr["units"][$cV->slug] = $pV->priceUnit;
				$returnArr["crops"][$year][$cV->slug]["US-" . $pV->stateAbrv] = round($pV->price, 2);
			}
		}
		return json_encode($returnArr);
	}
	
	
	public function getCSDPricing($year, $cropSlug) {		
		$returnArr = ["crops" => [$year => [$cropSlug => [] ] ] ];
		$cropObj = \App\CrowdsourcedDataProduceTypes::findBySlug($cropSlug);
		$corr = DB::table('csdPrice_csdProduceType')->where('produceType_id', $cropObj->id)->whereRaw('YEAR(created_at) = "' . $year . '"')->get();
		$priceIDs = [];
		foreach ($corr as $k => $v) {
			$priceIDs[] = $v->price_id;
		}
		$prices = DB::table('csdPrices')->select(DB::raw('stateAbrv, AVG(price) AS price'))->whereIn('id', $priceIDs)->groupBy('stateAbrv')->get();
		foreach ($prices as $pK => $pV) {
			$returnArr["crops"][$year][$cropSlug]["US-" . $pV->stateAbrv] = round($pV->price, 2);
		}
		return json_encode($returnArr);
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
