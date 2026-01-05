<script>
    // Check if the user is currently typing in an input field to avoid refreshing while typing
    function shouldRefresh() {
        const activeElement = document.activeElement;
        const isInput = activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA';
        return !isInput; 
    }


    setTimeout(function(){
       if (shouldRefresh()) {
           window.location.reload();
       } else {
           // If user is typing, wait another 5 seconds before trying again
           console.log("User is typing, postponing refresh...");
           setTimeout(arguments.callee, 5000);
       }
    }, 30000); // 30000 ms = 30 seconds
</script>
