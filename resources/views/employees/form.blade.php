<div class="modal-header">
    <h4 class="modal-title">{{ $title }} Employee</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    {{ html()->modelForm($form_details,'POST', route('employees.store', [$id]))->attribute('id', 'employee_form')->attribute('enctype', 'multipart/form-data')->open() }}
    <div class="col-lg-12">
        <div class="col-lg-3"></div>
        <div class="col-lg-5">
            <div class="form-group">
                {{ html()->label('Full Name <span style="color:red;">*</span>')->class('form-label')->for('fullname') }}
                {{ html()->text('fullname')->class('form-control fullname')->placeholder('Enter Full Name') }}
                <span style="color:red;" class="fullname_error"></span>
            </div>
            @if(Auth::guard('admin')->check())
            <div class="form-group">
                {{ html()->label('Companies <span style="color:red;">*</span>')->class('form-label')->for('company_id') }}
                @php
                $companies_array = array();
                $companies_array[''] = 'Select Company';
                if (!empty($companies_list->first())) {
                    foreach ($companies_list as $value) {
                        $companies_array[$value->id] = $value->name;
                    }
                }

                if (isset($form_details->company_id) && !empty($form_details->company_id)) {
                    $add_edit_company = $form_details->company_id;
                } else {
                    $add_edit_company = '';
                }
                @endphp
                {{ html()->select('company_id', $companies_array, $add_edit_company)->class('form-control company_id') }}
                <span style="color:red;" class="company_error"></span>
            </div>
            @endif
            
            <div class="form-group">
                {{ html()->label('Email <span style="color:red;">*</span>')->class('form-label')->for('email') }}
                {{ html()->email('email')->class('form-control email')->placeholder('Enter Email') }}
                <span style="color:red;" class="email_error">{{$errors->first('email')}}</span>
            </div>
            <div class="form-group">
                {{ html()->label('Phone <span style="color:red;">*</span>')->class('form-label')->for('phone') }}
                {{ html()->text('phone')->class('form-control phone') }}
                <span style="color:red;" class="phone_error"></span>
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

    $(function () {
        $('.save_btn').click(function () {
            var fullname = $(".fullname").val();
            var company_id = $(".company_id").val();
            var email = $(".email").val();
            var phone = $(".phone").val();
            var id = "{{ $id }}";

            $.ajax({
                url: "{{route('employees.formValidation')}}", // json datasource
                type: "POST", // method  , by default get
                data: {_token: $('meta[name="csrf-token"]').attr('content'), fullname: fullname, email: email, phone: phone, company_id:company_id, id: id},
                success: function (data) {

                    if (data != '') {
                        
                        var result = JSON.parse(data);
                        $(".fullname_error").html(result.fullname);
                        $(".company_error").html(result.company_id);
                        $(".email_error").html(result.email);
                        $(".phone_error").html(result.phone);

                    } else {
                        $(".fullname_error").html('');
                        $(".company_error").html('');
                        $(".email_error").html('');
                        $(".phone_error").html('');

                        $('#employee_form').submit();


                    }
                }
            });
        });
    });

</script>