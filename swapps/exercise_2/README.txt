##------------ AUTHOR : EGBE EUGENE AGBOR | AS ASPIRANT DEV@SWAPPS --------------##

#####################################################

### EXercise 2 -- HTML and JavaScript and php skills -- Search Engine for AWS #####

#####################################################


**** Added files to produce new folder structure


##-----------------------  NEW FOLDER STRUCTURE -------------------------------------------------------#

    amazonewebservice-|
                       |
                        - swapps -
                                  |_.idea(dir)
                                  |
                                  |_exercise_1(directory for the code-base for first exercise
                                             |
                                             |_Client.php {handle the server side code}
                                             |_Server.php {handle the client side code}
                                             |_index.php{communication b/w client and server}
                                             |*clientScript.js {contains javascript codes}
                                             |*Stylesheet.css { Custom styles for page}
                                             |*indexhtml {View for search}

###---------------------------------------------------------------------------------------------------------#

##---------------------------------------###
Usage:
        - Run index.html and type a search querry ( Like the Name of a book e.g Algorithms )
        - click on the search button to search
        - On the current page if data appears, click on the "More" Button to paginate

        Results:
                 - The results obtained are as a result from the request sent to the signed URL based on the
                    client parameters provided (lines {19 ,20} interface.php used by the server.

                 - The server arranges the output in a tabular form to show complete access of the objects
                   in a clearer fashion.

##----------------------------------------###

********************** CODE STATUS = WORKABLE WITH LIVE RESULTS ***********************

Todo: - Load progress bar on sending request
