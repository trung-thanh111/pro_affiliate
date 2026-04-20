-- Tài liệu phát triển giao diện layout các trang các section theo yêu cầu

\*\* yêu cầu:

1. khi nhận yêu cầu phát triển giao diện layout các trang các section theo yêu cầu thì phải bám sát yêu cầu, không được tự ý thay đổi yêu cầu nhé
2. khi mà ui đã có từ các component có sẵn thì phải dùng lại nhé, tránh tạo mới nhé, và sửa lại nhé (ngoại trừ có yêu cầu khác)
3. Ưu tiên class bootstrap 5.3 > scss (không style inline nhé tuyệt đối)
4. **Quy định kết hợp Bootstrap 5.3 + SCSS**:
    - Ưu tiên sử dụng utility classes của Bootstrap 5.3 cho các mục đích:
        - Layout & Structuring: `d-flex`, `flex-wrap`, `align-items-center`, `justify-content-between`, v.v.
        - Responsive: `flex-column flex-lg-row`, `d-none d-md-block`, v.v.
        - Styling cơ bản: `list-unstyled`, `text-decoration-none`, `text-uppercase`, `fw-bold`.
    - Chỉ sử dụng SCSS tùy chỉnh khi:
        - Bootstrap 5.3 không hỗ trợ (VD: CSS variables tùy chỉnh `--s-*`, `--ink-*`, `--canvas`).
        - Các hiệu ứng phức tạp (VD: `&:hover` với transform/shadow, active states đặc thù).
        - Các giá trị pixel chính xác 100% không nằm trong scale mặc định của Bootstrap.
    - Mục tiêu: Giảm thiểu kích thước file `app.scss`, tận dụng tối đa sức mạnh của framework có sẵn.
5. tự hiểu là có reponsive (mobile, tablet -> thật chính xác nhé) cả khi không yêu cầu nhé (ưu tiên class bootstrap 5.3 > scss (không style inline nhé tuyệt đối))
6. làm đơn giản hiệu quả cao đạt đúng yêu cầu mong muốn (không rườm rà nhé, không bị duplicate code nhé)
7. khi làm tuân thủ dùng lại các biến có sẵn thay vì tự set nhé như --s,..... trong app.scss nhé
8. khi làm ưu tiên mock data từ db ra nhé tùy theo bảng phù hợp nhé và làm đúng flow admin (repository, service, controller, view,... chứ không phải cứ muốn call ở đâu cũng được nhé -> tôi cần bài bản bảo mật chính sác theo flow codebase đang dùng nhé ), còn nếu mock ra mà null phải có fallback đúng case đó content đúng nhé tùy vào trường hợp yêu cầu đó là gì nữa nhé
9. khi làm mà có js thì phải tạo file js với tên phù hợp và đặt đúng vị trí và gọi lại cho chính page đó dùng thôi nhé (nếu page khác dùng thì tự nó call sau), không được đặt js trong page, component, layout, view,.... nhé (trừ khi có yêu cầu khác)
10. không được bỏ soát ý nào trong đây hết khi thực hiện yêu cầu
11. đối với data dùng chung lại nhiều page thì phải tối ưu cách gọi data nhé (tránh gọi lại nhiều lần, tránh gọi dư thừa khi không dùng (vd như ở page này đùng nhưng page khác không dùng nhưng vẫn gọi data đó -> nhiều như thế sẽ gây nặng load lâu nhé -> chú ý điểm này ))
12. làm theo flow codebase hiện tại nhé không được tự ý tạo khác riêng trừ khi có yêu cầu
13. làm chuẩn SEO nhé (dùng thẻ semantic để gg có thể đọc tốt hơn page khi SEO) và chú ý cả content và đầy đủ thông tin trong thẻ nhé tránh bị mất seo
    -- note lại lỗi sau khi fix về layout bên dưới để tránh gặp lại lỗi đó:
14. Khi lấy và fill data tránh n+1 query nhé, chú ý query cho chính xác nhé và query tối ưu performance nhé

-
