const divAlert = document.querySelectorAll(".alert");

divAlert.forEach(function(div){
    if(div.childNodes.length > 1){
        div.style.visibility = "visible";
        setTimeout(function(){
            hideSlowly(div);
        },5000); //5secondes
    }
})
    
function hideSlowly(item){
    let opacityDiv = 1;
    const interval = 100; //10 millisec beetween each animation step
    const opacityDiminution = 0.25;

    setInterval(function(){
        if(opacityDiv <= 0){
            item.style.visibility = "hidden"; //when opacity 0 then hide div
        }else{
            opacityDiv -= opacityDiminution; //reduce opacity up to 0
            item.style.opacity = opacityDiv; //set opacity in item
        }
    }, interval);

}