<?php

namespace Modules\Orders\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Orders\Models\Order;


class OrderCreatedAdminNotification extends Notification
{
    use Queueable;

    protected Order $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Order $order )    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($user)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($user)
    {

        return (new MailMessage)
            ->from('covid-19@eastclinic.ru', 'Ист клиник')
            ->subject('Заявка на оплату услуг в Ист клиник')
            ->view('orders::emails.order_created_admin', ['order' => $this->order]);


    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'pay_id' => $this->order->pay_id,
            'pay_url' => $this->order->pay_url,
//            'amount' => $this->order->sum,
            // Add any other data you want to store in the database notification
        ];
    }

    protected function purchasesAsTable(Order $order):string{
        $order->load(['purchases']);
        if(!$order->purchases) return '';//<<<<<<<<<<<<<<<
        $t = '<table style="border: 1px solid black;">
              <tr>
                <th style="background-color: grey; color: white;">Наименование</th>
                <th style="background-color: grey; color: white;">Количество</th>
                <th style="background-color: grey; color: white;">Стоимость</th>
              </tr>';

        foreach ($order->purchases as $good){
            $t .= '<tr>
                    <td style="background-color: yellow;">'.$good->name.'</td>
                    <td style="background-color: green; color: white;">'.$good->count.'</td>
                    <td style="background-color: green; color: white;">'.$good->price.'</td>
                  </tr>';
        }
        $t .= '<tr>
                    <td style="background-color: green; color: white;">Итого:</td>
                    <td style="background-color: green; color: white;">'.$order->sum.'</td>
               </tr>';
        $t .= '</table>';



        return $t;
    }

    protected function userContactsTable($order):string{
        $t = '<table style="border: 1px solid black;">';

        foreach ($order->contacts as $contact){
//            $t .= '<tr>
//                    <td style="background-color: yellow;">'.$contact->name.'</td>
//                    <td style="background-color: green; color: white;">'.$contact->count.'</td>
//                    <td style="background-color: green; color: white;">'.$contact->price.'</td>
//                  </tr>';
        }

//        $t .= '<tr>
//                    <td style="background-color: green; color: white;">Итого:</td>
//                    <td style="background-color: green; color: white;">'.$order->sum.'</td>
//                  </tr>';
        $t .= '</table>';
        return '';
    }


}
