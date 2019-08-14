
var enableKey = document.getElementById("enableKey");
var desc = document.getElementById("enable-key");

document.getElementById("googleApiKey").onsubmit = function() {
	var check = checkEnableKey();
	if (check == false) { return false; }
};

function checkAlert(){
	if (enableKey.checked ==  true) {
		if ( desc.classList.contains('alert-enable-key') ){
			desc.classList.remove('alert-enable-key');
		}
	}else{
		checkEnableKey();
	}
}

function checkEnableKey() {
	
	if (enableKey.checked ==  false) {
		
		if ( desc.classList.contains('alert-enable-key') ){
			desc.classList.remove('alert-enable-key');
			setTimeout(function() {
		      desc.classList.add('alert-enable-key');
		    }, 300);
		}else{
			desc.classList.add('alert-enable-key');
		}
		return false;
	}
}