<div v-if="orders">
  <div>
    <div class='pa-2 red lighten-5' d-flex justify-space-between align-center>
        <div class='red lighten-5'>
            <span d-flex align-center>
                <strong>Товаров на сумму:</strong>
                <span class='font-weight-medium ml-2'>{{ $data['product_total'] }}  RUB</span>
            </span>
            <span d-flex align-center>
                <strong>Клиентов:</strong>
                <span class='font-weight-medium ml-2'>{{ $data['clients_count'] }}</span>
            </span>
            <span d-flex align-center>
                <strong>Доставка в города:</strong>
                <span class='font-weight-medium ml-2'>{{ implode(' ,', $data['cities']->toArray()) }}</span>
            </span>
        </div>
    </div>
  </div>
          
  <div>
    <table style="border: 1px solid #dee2e6; margin: 15px 0; width: 100%; max-width: 600px;">
      @foreach($data['vendor_codes'] as $vendor)
      <tr>
        <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $vendor['vendor_code'] }}</td>
        <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">
          <div class='transparent'>
            <span class='item_info'>
              <span-content>
              <span-title>{{ $vendor['name'] }} </span-title>
              <span-sub-title>{{ $vendor['type'] }}</span-sub-title>
              <span-sub-title>{{ $vendor['color'] ? 'Цвет: ' . $vendor['color'] : '' }} </span-sub-title>
              <span-sub-title>{{ $vendor['length'] ? 'Длина: ' . $vendor['length'] : '' }}</span-sub-title>
              </span-content>
            </span>
          </div>
        </td>
        <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $vendor['count'] }}</td>
        <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $vendor['price_final'] }}</td>
      </tr>
      @endforeach
    </table>
  </div>
        
  <div>
    @foreach($data['clients'] as $phone => $client)
      <div>
        <div class='pa-3 mt-3'>
          <span class='font-weight-medium title'>{{ $phone }}</span>
        </div>
        @foreach($client as $order)
          <div v-for="order in client">
            <table hide-actions style="border: 1px solid #dee2e6; margin: 15px 0; width: 100%; max-width: 600px;">
              @foreach($order['products'] as $index => $product)
                <tr>
                  <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $product['vendor_code'] }}</td>
                  <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">
                    <div class='transparent'> <span class='item_info'>
                        <span-content>
                          <span-title>{{ $product['name'] }} </span-title>
                          <span-sub-title>{{ $product['type'] }}</span-sub-title>
                          <span-sub-title>{{ $product['color'] ? 'Цвет: ' . $product['color'] : '' }} </span-sub-title>
                          <span-sub-title>{{ $product['length'] ? 'Длина: ' . $product['length'] : '' }}</span-sub-title>
                        </span-content>
                      </span>
                    </div>
                  </td>
                  @if($index == 0)
                    <td rowspan="100%">г. {{ $order['delivery_city'] }}<br />{{ $order['delivery_address'] }}</td>
                  @endif
                  <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $product['quantity'] }}</td>
                  <td style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">{{ $product['price_final'] }}</td>
                </tr>
              @endforeach
              <tr>
                <td colspan="100%" style=" border: 1px solid #dee2e6; padding: 5px; font-size: 12px;">
                  <strong>{{ $order['total'] }}</strong>
                </td>
              </tr>
            </table>
          </div>
        @endforeach
      </div>
      @endforeach
  </div>
</div>