/**
 * Created by eugene on 11/25/16.
 */

//get a reference to the  modal
var modal = document.getElementById('myModal');

//we declare the page number to be used for pagination on the server code
var pageNumber = 1;

$('#more').prop('disabled', true);

//this is the function where our request is sent
function searchServer(type) {


    //we get reference to the modal
    var modal = document.getElementById('myModal');

    // we get reference to the search box
    var str = $('#search').val();

    if (str.length == 0) {

        // document.getElementById("result").innerHTML = "there is no text";
        alert('Please Add a Search term');
        return;

    } else {

        if (type == 'search'){

            pageNumber = 1;
        }

        $.ajax({url: "index.php?search="+str+"&page="+pageNumber, success: function(result){

            // show the more button on success
            $('#more').prop('disabled', false);

            //in case where the result is empty from a bad querry, then we disable the more button
            if(result == ""){

                //prepare the modal message and display the modal
                document.getElementById('errormsg').innerHTML = "OOps! Something went wrong please Check your" +
                    "search Term";
                modal.style.display = "block";

                $('#more').prop('disabled', true);
                return false;
            }

            //we now display the result on the div of the html page
            $('#result').html(result);
            pageNumber +=1;

        },error: function (request, status , error ) {

            //In this case we have the server returning an error
            document.getElementById('errormsg').innerHTML = "OOps! Something went wrong please Check your" +
                "search Term:: Server Error"+request.responseText;

            modal.style.display = "block";

        } } );

    }


}

function closeModal() {

    document.getElementById('errormsg').innerHTML = "";
    modal.style.display = "none";
}

