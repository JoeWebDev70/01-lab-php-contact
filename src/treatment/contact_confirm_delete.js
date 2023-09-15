const btnValidate = document.querySelector("#btnValidate");
const btnCancel = document.querySelector("#btnCancel");
const btnSelection = document.querySelector("#btnSelection");
btnSelection.disabled = true;
const Selects = document.querySelectorAll(".select");
const SelectsOne = document.querySelectorAll(".selectOne");
let contactId = [];
let oneChecked = [];

btnValidate.addEventListener("click", function(){ //send values to php for process them
    if(contactId.length > 0){
        console.log(contactId);
        //HTTP request : URL and object
        fetch('http://php-dev-1.online/treatment/contact_delete.php',{ 
            method: 'POST',
            body: JSON.stringify({'idcontact':contactId}),  //object of request transformed in json
            headers: {  //object is send to the server as
                "content-Type" : 'application/json' 
            }   
        }).then((response) => { //receive the server response
            if(response.ok){ //check if response is HTTP OK == 200
                return response.json(); //then send the response to a further promise
            }
        })
        .then((data) => { //process the response send by the previous promise
            contactId.length = 0; //clear array
            if(data == true){
                window.location.href = "http://php-dev-1.online/treatment/contact_display.php";
            }else{//other responses send the user on login page set error in console
                console.log('error on delete contact : ' , data);
                window.location.href = "http://php-dev-1.online/treatment/logout.php";
            }
        })
        .catch(error => { //if there was some error 
            console.error("error fetch : ", error);
        });
    }
});

btnCancel.addEventListener("click", function(){ 
    Selects.forEach(function(select){//uncheck checkbox
        if(select.checked){
            select.checked = false;
        }
    })
    contactId.length = 0; //clear array
    btnSelection.disabled = true; //disable btn delete selection
})

SelectsOne.forEach(function(selectOne){ //get value of single contact to delete
    selectOne.addEventListener("click", function(){
        contactId.push(selectOne.value);
    });
});

btnSelection.addEventListener("click", function(){ //get values of selected contacts
    Selects.forEach(function(select){
        if(select.checked){
            contactId.push(select.value);
        }
    })
});

Selects.forEach(function(select){ //disable button for deleting selection if no checkbox is check
    select.addEventListener("change", function(){
        if(select.checked){
            oneChecked.push(select);
        }else{
            oneChecked.pop(select);
        }

        if(oneChecked.length > 0){
            btnSelection.disabled = false;
        }else{
            btnSelection.disabled = true;
        }
    });
});