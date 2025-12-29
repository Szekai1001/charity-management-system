<x-mail::message>
# Hello {{ $userName }},

@if(strtolower($status) === 'approved')
<x-mail::panel>
🎉 **Congratulations!**  
Your application has been approved.  
You can now log in to your account and start using our services.
</x-mail::panel>

<x-mail::button :url="url('/')">
Go to Website
</x-mail::button>
@elseif(strtolower($status) === 'rejected')
<x-mail::panel>
⚠️ **We’re sorry to inform you.**  
Your application has been rejected.  
Please contact our support team for more details or future opportunities.
</x-mail::panel>
@endif

Thanks,<br>
**PKKM Batu Pahat**
</x-mail::message>
