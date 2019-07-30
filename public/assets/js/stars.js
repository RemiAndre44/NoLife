function changeClass(id){
	idc = "#star"+id;
	var element = $(idc).attr('class');
	for(i=1;i <= id; i++){
		if(element == "far fa-star"){
			$("#star"+ i).removeClass("far fa-star").addClass('fas fa-star');
		}else{
			$("#star"+i).removeClass('fas fa-star').addClass('far fa-star');
		}
	}	
}

function shootClass(){
	for(i=1;i<=10;i++){
		if($("#star"+i).attr('class') == "fas fa-star"){
			$("#star"+i).removeClass('fas fa-star').addClass('far fa-star');
		}
	}
}

function onClickBtnStar(event){
    event.preventDefault();

    const url = this.href;
    const icone = this.querySelector('i');

    axios.get(url).then(function(response){
        const stars = response.data.stars;
        const reste = 10 - stars;
        for(i=1;i<=stars;i++){
	        icone.style.color = "#adb5bd";
        }
        if(stars != 10){
        	for(i=stars;i<= 10;i++){
        		icone.style.color = "#ffffff";
        	}
        }
        
    }).catch(function(error){
        if(error.response.status === 403){
            show_toast(2);
        }else{
            show_toast(1);
        }
    });
}


document.querySelectorAll('a.js-star').forEach(function(link){
    link.addEventListener('click', onClickBtnLike);
})
