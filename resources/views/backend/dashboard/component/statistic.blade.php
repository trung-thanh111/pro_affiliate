<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right">Tháng</span>
                <h5>Đơn hàng trong tháng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['orderCurrentMonth'] }}</h1>
                {!! growHtml($orderStatistic['grow']) !!}
                <small>Tăng trưởng so với tháng trước</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">Tổng số đơn hàng</span>
                <h5>Orders</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['totalOrders'] }}</h1>
                <div class="stat-percent font-bold text-info">{{ growth($orderStatistic['totalOrders'], $orderStatistic['cancleOrders']) }}% </div>
                <small>Số đơn hủy {{ $orderStatistic['cancleOrders']  }} chiếm </small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right">Total</span>
                <h5>Tổng doanh thu</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ convert_price($orderStatistic['revenue'], true) }}đ</h1>
                <small>Tổng doanh thu</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-danger pull-right">Customer</span>
                <h5>Tổng số khách hàng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $customerStatistic['totalCustomers'] }}</h1>
                <small>Tổng số khách hàng</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Thống kê Sản phẩm & Danh mục</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="widget style1 navy-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-cube fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <span> Tổng sản phẩm </span>
                                    <h2 class="font-bold">{{ $productStatistic['totalProducts'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-striped table-bordered table-hover" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="position: sticky; top: 0; background: white; z-index: 10;">
                                        <th class="text-center" style="width: 50px;">STT</th>
                                        <th>Tên danh mục</th>
                                        <th class="text-center" style="width: 150px;">Số lượng sản phẩm</th>
                                        <th class="text-center" style="width: 100px;">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($productStatistic['catalogueStatistic']) && count($productStatistic['catalogueStatistic']))
                                        @foreach($productStatistic['catalogueStatistic'] as $key => $val)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <span class="font-bold text-navy">{{ $val['name'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-primary">{{ number_format($val['count']) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $percentage = ($productStatistic['totalProducts'] > 0) 
                                                            ? round(($val['count'] / $productStatistic['totalProducts']) * 100, 1) 
                                                            : 0;
                                                    @endphp
                                                    <span class="text-muted">{{ $percentage }}%</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có dữ liệu thống kê</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>