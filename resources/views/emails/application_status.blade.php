<x-mail::message>
# Hello {{ $userName }},

@if(strtolower($status) === 'approved')
<x-mail::panel>
🎉 **Congratulations!** Your application for the **{{ $applicationType }}** program has been approved.  

@if($applicationType === 'beneficiary')
You can now log in to your account and start using our services.
@else
Your records have been updated in our system. You do not need to take any further action at this time.
@endif
</x-mail::panel>

{{-- Only show the button if they are a beneficiary --}}
@if($applicationType === 'beneficiary')
<x-mail::button :url="url('/')">
Go to Website
</x-mail::button>
@endif

@elseif(strtolower($status) === 'rejected')
<x-mail::panel>
⚠️ **Application Update** We regret to inform you that your application for the **{{ $type }}** program has been rejected.  
Please contact our support team if you have any questions.
</x-mail::panel>
@endif

Thanks,<br>
**PKKM Batu Pahat**
</x-mail::message>