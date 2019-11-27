@if ($message = Session::get('success'))
<div class="alert alert-success alert-block" id="success_alert">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
<script>
    $(document).ready(function(){
        setTimeout(function(){ 
            $('#success_alert').hide();
        }, 10000);    
    });
</script>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block" id="danger_alert">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
<script>
    $(document).ready(function(){
        setTimeout(function(){ 
            $('#danger_alert').hide();
        }, 10000);    
    });
</script>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block" id="warning_alert">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
<script>
    $(document).ready(function(){
        setTimeout(function(){ 
            $('#warning_alert').hide();
        }, 10000);    
    });
</script>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block" id="info_alert">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
<script>
    $(document).ready(function(){
        setTimeout(function(){ 
            $('#info_alert').hide();
        }, 10000);    
    });
</script>
@endif

@if ($errors->any())
<!--<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    Please check the form below for errors
</div>-->
@endif