eric = new Object();
eric.common = {
	go : function(url){
		if(url)
			window.location.href = url;
		else
			history.go(-1);
	},
	
	datepicker : function(id){
		var yearFrom=new Date().getYear()-60+1900;
		var yearTo=new Date().getYear()-18+1900;  
		$('#'+id).datepicker({
			format: "yyyy-mm-dd",
			yearRange : yearFrom+':'+yearTo,
		}); 
	}
};
eric.request = {
	post : function(form, url){
		form.action = url;
		form.method = 'post';
		return true;
	},	
	
	ajaxPost : function(url){
		
	}
};

eric.response = {
	success : function(msg, url){
		if(!msg) msg = '提交成功！';
		jSuccess(
				msg,
			    {
			      autoHide : true, // added in v2.0
			      TimeShown : 1000,
			      HorizontalPosition : 'center',
			      onCompleted : function(){
			    	  window.location.href = url;
			    }
			  }
		);
	},
	error : function(msg){
		if(!msg) msg = '提交成功！';
		jError(
				msg,
			    {
			      autoHide : true, // added in v2.0
			      TimeShown : 3000,
			      HorizontalPosition : 'center',
			      onCompleted : function(){ // added in v2.0
			    }
			  }
		);
	}
};