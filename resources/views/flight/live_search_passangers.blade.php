@foreach($searchPassangers as $searchPassangerIndex => $searchPassanger)
<li class="live_search_item">
    <a class="live_search_product_link" href="javascript:void(0)" onclick="autoFillUpForm({{$searchPassangerIndex}})">
        <h6 class="live_search_product_title">
            {{$searchPassanger->title}} {{$searchPassanger->first_name}} {{$searchPassanger->last_name}} ({{$searchPassanger->contact}})

            <input type="hidden" id="passanger_title_{{$searchPassangerIndex}}" value="{{$searchPassanger->title}}">
            <input type="hidden" id="passanger_first_name_{{$searchPassangerIndex}}" value="{{$searchPassanger->first_name}}">
            <input type="hidden" id="passanger_last_name__{{$searchPassangerIndex}}" value="{{$searchPassanger->last_name}}">
            <input type="hidden" id="passanger_email_{{$searchPassangerIndex}}" value="{{$searchPassanger->email}}">
            <input type="hidden" id="passanger_contact_{{$searchPassangerIndex}}" value="{{$searchPassanger->contact}}">
            <input type="hidden" id="passanger_type_{{$searchPassangerIndex}}" value="{{$searchPassanger->type}}">
            <input type="hidden" id="passanger_dob_{{$searchPassangerIndex}}" value="{{$searchPassanger->dob}}">
            <input type="hidden" id="passanger_document_type_{{$searchPassangerIndex}}" value="{{$searchPassanger->document_type}}">
            <input type="hidden" id="passanger_document_no_{{$searchPassangerIndex}}" value="{{$searchPassanger->document_no}}">
            <input type="hidden" id="passanger_document_expire_date_{{$searchPassangerIndex}}" value="{{$searchPassanger->document_expire_date}}">
            <input type="hidden" id="passanger_document_issue_country_{{$searchPassangerIndex}}" value="{{$searchPassanger->document_issue_country}}">
            <input type="hidden" id="passanger_nationality_{{$searchPassangerIndex}}" value="{{$searchPassanger->nationality}}">
            <input type="hidden" id="passanger_frequent_flyer_no_{{$searchPassangerIndex}}" value="{{$searchPassanger->frequent_flyer_no}}">
        </h6>
    </a>
</li>
@endforeach
