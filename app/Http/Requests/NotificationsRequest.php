<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (in_array($this->route()->getName(), ['send.notifications'])) {
            return true; //$this->user()->can('send-contact');
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if (in_array($this->route()->getName(), ['send.notifications'] )) {
            $rules = [
                'ids' => 'required|array|min:1',
                'subject' => 'required|min:3',
                'content' => 'required|min:3',
            ];
        }  

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        if (in_array($this->route()->getName(), ['send.notifications'] )) {
            return [
                'ids' => 'تحديد الصفوف',
                'subject' => 'الموضوع',
                'content' => 'المحتوي',
            ];
        } 
        return $attributes;
    }

    public function messages()
    {
        $messages = parent::messages();
        if ($this->route()->getName() == 'send.notifications') {
            $messages['subject.required'] = 'موضوع الاشعار مطلوب ';
            $messages['subject.min']      = 'لابد الا بقل طول الموضوع عن ثلاث احرف ';
            $messages['content.required'] = 'المحتوي مطلوب ';
            $messages['content.min']      = 'لابد الا  يقل  طول  المحتوي  عن ثلاث احرف ';            
             $messages['ids.required']    = 'لابد من اختيار المرسل اليه ';
            $messages['ids.min']          = 'لابد من اختيار مرسل اليه واحد علي الاقل ';
            $messages['ids.array']        = 'لابد ان يكون  المرسلين اليه  علي شكل مصفوفه ';
           
            

        }
        return $messages;
    }
}
