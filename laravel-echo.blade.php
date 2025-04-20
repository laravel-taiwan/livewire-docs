* [簡介](#introduction)
* [監聽器](#listeners)
* [私人和在線狀態頻道](#private-presence-channels)

## 簡介 {#introduction}

Livewire與Laravel Echo搭配使用，可在網頁上使用WebSockets提供即時功能。

@component('components.warning')
此功能假設您已安裝了Laravel Echo，並且`window.Echo`對象在全域範圍內可用。有關更多信息，請查看<a href="https://laravel.com/docs/broadcasting#client-side-installation">文檔</a>。
@endcomponent

考慮以下Laravel事件：

@component('components.code-component')
@slot('class')
class OrderShipped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn()
    {
        return new Channel('orders');
    }
}
@endslot
@endcomponent

假設您像這樣使用Laravel的廣播系統觸發此事件：

@component('components.code', ['lang' => 'php'])
event(new OrderShipped);
@endcomponent

通常，您會像這樣在Laravel Echo中聆聽此事件：

@component('components.code', ['lang' => 'js'])
    Echo.channel('orders')
        .listen('OrderShipped', (e) => {
            console.log(e.order.name);
        });
@endcomponent

## 監聽器 {#listeners}

使用Livewire，您只需在`$listeners`屬性中註冊它，並使用一些特殊語法來指定它是來自Echo。

@component('components.code-component')
@slot('class')
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;

    // 特殊語法: ['echo:{channel},{event}' => '{method}']
    protected $listeners = ['echo:orders,OrderShipped' => 'notifyNewOrder'];

    public function notifyNewOrder()
    {
        $this->showNewOrderNotification = true;
    }
}
@endslot
@endcomponent

如果您的Echo頻道中有變數（例如訂單ID），您可以使用`getListeners()`函數代替`$listeners`陣列。

@component('components.code-component')
@slot('class')
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;
    public $orderId;

```php
public function getListeners()
{
    return [
        "echo:orders.{$this->orderId},OrderShipped" => 'notifyNewOrder',
    ];
}

public function notifyNewOrder()
{
    $this->showNewOrderNotification = true;
}
}
@endslot
@endcomponent

@component('components.warning')
<code>getListeners()</code> 只會在元件掛載時動態生成監聽器的名稱。一旦監聽器設置完成，就無法更改。
@endcomponent

@component('components.warning')
請注意，如果您正在使用[模型廣播](https://laravel.com/docs/10.x/broadcasting#model-broadcasting)，您需要在事件前加上 '.' 以便正確監聽事件，例如 <code>.MessageCreated</code>。
@endcomponent


現在，Livewire 將攔截來自 Pusher 的接收事件，並相應地執行。

## 私人與存在頻道 {#private-presence-channels}

與常規公共頻道類似，您還可以監聽廣播到私人和存在頻道的事件：

@component('components.warning')
    請確保您已正確定義您的<a href="https://laravel.com/docs/master/broadcasting#defining-authorization-callbacks">認證回調</a>。
@endcomponent

@component('components.code-component')
@slot('class')
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getListeners()
    {
        return [
            "echo-private:orders.{$this->orderId},OrderShipped" => 'notifyNewOrder',
            // Or:
            "echo-presence:orders.{$this->orderId},OrderShipped" => 'notifyNewOrder',
        ];
    }

    public function notifyNewOrder()
    {
        $this->showNewOrderNotification = true;
    }
}
@endslot
@endcomponent

這使您能夠對這些頻道上的 listen 事件做出反應，並使用 `OrderShipped` 事件名稱。 您還可以通過稍微更改語法來訪問存在頻道的 `joining | leaving | here` 事件。

```php
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getListeners()
    {
        return [
            // Public Channel
            "echo:orders,OrderShipped" => 'notifyNewOrder',
            
            // Private Channel
            "echo-private:orders,OrderShipped" => 'notifyNewOrder',
            
            //Presence Channel
            "echo-presence:orders,OrderShipped" => 'notifyNewOrder',    // Listen
            "echo-presence:orders,here" => 'notifyNewOrder',            // Here
            "echo-presence:orders,joining" => 'notifyNewOrder',         // Joining
            "echo-presence:orders,leaving" => 'notifyNewOrder',         // Leaving
        ];
    }

    public function notifyNewOrder()
    {
        $this->showNewOrderNotification = true;
    }
}
```
