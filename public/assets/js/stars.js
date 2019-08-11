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
    
    axios.get(url).then(function(response){
        const stars = response.data.stars;
        console.log(stars)
        for(i=1;i<=stars;i++){
            var selecteur = "i#star"+i;
            $(selecteur)[0].classList.replace("far", "fas");
        }
        if(stars != 10){
            var loop = parseInt(stars) +1;
        	for(i=loop;i<= 10;i++){
        		var selecteur = "i#star"+i;
                $(selecteur)[0].classList.replace("fas", "far");
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
    link.addEventListener('click', onClickBtnStar);
})
