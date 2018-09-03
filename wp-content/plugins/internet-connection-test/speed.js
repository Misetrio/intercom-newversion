(function($){
	var dataFileLocation = window.SPEED_TEST_CONFIG.DATA_FILE_LOCATION;
	var needToStopSpeedTesting = false;
	var request = null;
	var downloadedBytes = 0;
	var startTestTime = 0;
	var lastTestResult = null;
    var numTriesBeforeAutoStop = 5;
    var tries = 0;

	function getDataFile(callback){
        tries++;
        if(tries>numTriesBeforeAutoStop){
            var speed_test = $('.speed_test');
            speed_test.removeClass('stop');
            speed_test.addClass('retry');
            stopSpeedTesting();
        }else{
            var startTime = $.now();
            request = $.ajax({
                url: dataFileLocation + '?r=' + $.now(),
                method: "GET",
                success: function(){
                    var time = $.now() - startTime;
                    callback(time);
                }
            });
        }
	}

	var reaction = function(time){
		downloadedBytes += 1024*1024;
        var speed = downloadedBytes*8/(($.now() - startTestTime)/1000); // byte/sec
        var speed_mb = speed/(1024*1024);
        var speed_str = speed_mb.toFixed(2) + ' Mb/s';
		lastTestResult = speed_str;
        var scaled = $('.speed_test .stop .speed .scaled');
        var factor = Math.sqrt(speed_mb/20);
        if(factor>1){
            factor = 1;
        }
        var int = parseInt(factor*100);
        scaled.css('width', int+'%');
        var r = parseInt(255-factor*255);
        var g = parseInt(factor*255);
        var b = 0;
        scaled.css('background-color', 'rgb('+r+', '+g+', '+b+')');

		$('.speed_test .stop .speed .label').html(speed_str);
		if(!needToStopSpeedTesting){
			getDataFile(reaction);
		}
	};

	function startSpeedTest(){
        tries = 0;
		startTestTime = $.now();
        downloadedBytes = 0;
		needToStopSpeedTesting = false;
		getDataFile(reaction);
	}

	function stopSpeedTesting(){
		needToStopSpeedTesting = true;
        $('.speed_test .retry .speed .label').text(lastTestResult);
		request.abort();
	}

	$( document ).ready(function() {
		var speed_test = $('.speed_test');
		var start = $('.speed_test .start');
		var stop = $('.speed_test .stop .button');
		var retry = $('.speed_test .retry .button');
		start.click(function(){
				speed_test.removeClass('start');
				speed_test.addClass('stop');
				startSpeedTest();
		});

		stop.click(function(){
				speed_test.removeClass('stop');
				speed_test.addClass('retry');
				stopSpeedTesting();
		});

		retry.click(function(){
			speed_test.removeClass('retry');
			speed_test.addClass('stop');
			startSpeedTest();
		});
	});
})(jQuery);
