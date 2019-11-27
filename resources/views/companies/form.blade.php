<div class="modal-header">
    <h4 class="modal-title">{{ $title }} Company</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    {{ html()->modelForm($form_details,'POST', route('admin.companies.store', [$id]))->attribute('id', 'company_form')->attribute('enctype', 'multipart/form-data')->open() }}
    <div class="col-lg-12">
        <div class="col-lg-3"></div>
        <div class="col-lg-5">
            <div class="form-group">
                {{ html()->label('Company Name <span style="color:red;">*</span>')->class('form-label')->for('CompanyName') }}
                {{ html()->text('name')->class('form-control name')->placeholder('Enter Company Name') }}
                <span style="color:red;" class="name_error">{{$errors->first('name')}}</span>
            </div>
            <div class="form-group">
                {{ html()->label('Email <span style="color:red;">*</span>')->class('form-label')->for('email') }}
                {{ html()->email('email')->class('form-control email')->placeholder('Enter Email') }}
                <span style="color:red;" class="email_error">{{$errors->first('email')}}</span>
            </div>
            <div class="form-group">
                {{ html()->label('Password <span style="color:red;">*</span>')->class('form-label')->for('password') }}
                {{ html()->password('password')->class('form-control password')->value('') }}
                <span style="color:red;" class="password_error">{{$errors->first('password')}}</span>
            </div>
            <div class="form-group">
                @php
                if(!empty($form_details->website)){
                    $website_value = $form_details->website;
                }else{
                    $website_value = '';
                }
                @endphp
                {{ html()->label('Website <span style="color:red;">*</span>')->class('form-label')->for('website') }}
                {{ html()->input('website')->type('url')->name('website')->class('form-control website')->placeholder('Enter Website')->value($website_value) }}
                <span style="color:red;" class="website_error">{{$errors->first('website')}}</span>
            </div>
            <div class="form-group">
                {{ html()->label('Logo')->class('form-label')->for('logo') }}
                <div class="input-group">
                    <div class="custom-file">
                        {{ html()->file('image')->class('custom-file-input logo')->attribute('onchange', 'openFile(event)') }}
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                    </div>
                    <span style="color:red;" class="logo_error">{{$errors->first('logo')}}</span>
                    <img id='output' style="width: 150px;height: 150px;margin-top: 10px; display: none;" >
                    @if(!empty($form_details->logo))
                    <img id='output_edit' src="{{url($form_details->logo)}}" style="width: 150px;height: 150px;margin-top: 10px;" >
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3"></div>
    </div>
    {{ html()->form()->close() }}
</div>
<div class="modal-footer justify-content-between">
    {{ html()->button('Close')->class('btn btn-default')->attribute('data-dismiss','modal') }}
    {{ html()->button('Save')->class('btn btn-primary save_btn') }}
</div>

<script>
    var openFile = function (event) {
        var input = event.target;

        var reader = new FileReader();
        reader.onload = function () {
            var dataURL = reader.result;
            var output = document.getElementById('output');
            output.src = dataURL;
        };
        reader.readAsDataURL(input.files[0]);
        $(".logo_error").html('');
        $('#output').show();
        $('#output_edit').hide();
    };

    $(function () {
        $('.save_btn').click(function () {
            var name = $(".name").val();
            var email = $(".email").val();
            var website = $(".website").val();
            var password = $(".password").val();
            var logo = $(".logo").val();
            var id = "{{ $id }}";

            $.ajax({
                url: "{{route('admin.companies.formValidation')}}", // json datasource
                type: "POST", // method  , by default get
                data: {_token: $('meta[name="csrf-token"]').attr('content'), name:name, email:email, website:website, password:password, logo:logo, id:id},
                success: function (data) {
                    
                    if(data != ''){
                        console.log(data);
                        var result = JSON.parse(data);
                        $(".name_error").html(result.name);
                        $(".email_error").html(result.email);
                        $(".password_error").html(result.password);
                        $(".website_error").html(result.website);
                        $(".logo_error").html(result.logo);
                        
                    }else{
                        $(".name_error").html('');
                        $(".email_error").html('');
                        $(".password_error").html('');
                        $(".website_error").html('');
                        $(".logo_error").html('');
                        
                        $('#company_form').submit();
                        
                        
                    }
                }
            });
        });
    });

</script>