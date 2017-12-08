<html>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript">
    function loadSubmitResults() { //alert("kk");return false;
        $(function() {
            $('#DisplayDiv').load('dropbox_fileopen1.php');
        });
    }
</script>
<body>
    <div id="page">
<!--
        <form id="SubmitForm" method="post">
-->
            <div id="SubmitDiv">
                <input type="button" onclick="loadSubmitResults();"  value="new">
            </div>
<!--
        </form>
-->
        <div id="DisplayDiv">
            <!-- This is where test2.php should be inserted -->
        </div>
    </div>
</body>
