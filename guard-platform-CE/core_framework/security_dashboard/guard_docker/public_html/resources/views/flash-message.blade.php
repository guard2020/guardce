@if ($message = Session::get('success'))
<div class="alert alert-success alert-block center my-2">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-block center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{$errors->first() ? $errors->first() : 'Please check the form below for errors'}}
</div>
@endif

<style>
.center {
	text-align:center;
	margin: auto;
    margin-top: 10px;
	width: 30%;
	border: 1px solid #73AD21;
    border-radius: 25px;
	padding: 10px;
}
</style>
<script>
 $(document).ready(function () {

            setTimeout(function() {
                $('.alert-block').slideUp("slow");
            }, 3000);
});
</script>
