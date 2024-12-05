<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;


class UsersExport implements FromQuery, WithHeadings
{
    use Exportable;

    /**
     * Return the query for the data you want to export.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        //return User::query(); // You can customize the query if needed
        return User::select('id','name','email','mobile','amount','created_at')->orderBy('id','DESC')->get(); 
    }

    /**
     * Define the headings for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Mobile',
            'Amount',
            'Created At',
            'Updated At',
        ];
    }
}

