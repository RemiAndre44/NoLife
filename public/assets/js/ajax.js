function onClickBtnLike(event){
    event.preventDefault();

    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function(response){
        const likes = response.data.likes;
        spanCount.textContent = likes;

        if(icone.style.color == "blue"){
            icone.style.color = "";
        }else{
            icone.style.color = "blue";
        }
    }).catch(function(error){
        if(error.response.status === 403){
            show_toast(2);
        }else{
            show_toast(1);
        }
    });
}


document.querySelectorAll('a.js-like').forEach(function(link){
    link.addEventListener('click', onClickBtnLike);
})
