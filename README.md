
## Proje Hakkında

- Docker platformu kullanılmış olup 'sail' kullanılmıştır. [Kurulum ve detaylı bilgi için lütfen tıklayınız](https://laravel.com/docs/8.x/sail).
- Proje içerisinde dış kaynaklardan sadece docker platformu için yararlanılmıştır.
- Proje de PHP 8 ve Laravel 8 kullanılmıştır.
- Veritabanı olarak MySql kullanılmıştır.
- .env ayarlarınıza docker ip'niz ve docker mysql container ismini yazmanız gerekmektedir. | .env.example alıp düzenleye bilirsiniz.
- docker exec -it ideasoft_laravel.test_1 bash komutu ile laravel conteiner giriş yapınız. (Sizde container ismi farklı olabilir!)
- Kurulumdan sonra 'composer install' ardından 'php artisan migrate:refresh --seed' çalıştırmanız gerekmektedir.(tabloları ve örnek dataları oluşturmak için)
- Projenin rotaları api.php içerisindedir.
- Projenin kurulum yapıldıktan sonra POSTMAN üzerinden istekleri atabilirsiniz. Lütfen 'Headers' alanına KEY=Accept Value=application/json ekleyiniz
- Projenin testleri de yazılmış olup tests/Feature klasörünün altında yer almaktadır. Testleri çalıştırmak için 'php artisan test' komutunu çalıştırınız.
- PSR-12 standartlarına uygun şekilde kodlar yazılmıştır.
- Tüm soru ve görüşleriniz için halilomergurgan@gmail.com üzerinden irtibata geçebilirsiniz.

## Endpointler Hakkında
- Proje içerisinde 
    - 'Müşteri' listeleme, ekleme, düzenleme ve silme işlemlerini yapabilirsiniz.
    - 'Ürün' listeleme, ekleme, düzenleme ve silme işlemlerini yapabilirsiniz.
    - 'Order' listeleme, ekleme, düzenleme ve silme işlemlerini yapabilirsiniz.
    - 'Order Item' ekleme(add to cart, sipariş oluşturma v.b.) ve silme işlemlerini yapabilirsiniz.

- RESTful mimarisi kullanımına özen gösterilmiş olup RESTful api servisi oluşturulmuştur. Ayrıca Response için Response Class oluşturulmuştur. 

## Sipariş Ekleme Hakkında
- Sipariş eklenirken validasyonlar mevcuttur. Requests klasörü içerisindeki dosyalardan görebilirsiniz.
- Stock kontrolleri yapılmıştır. 
- Sipariş eklendiğinde stocktan alınan değer düşülmektedir.
- Sistemde kayıtlı müşteri olmadığında validasyona düşmektedir.
- Sipariş esnasında hata veya network bazlı hata olduğunda otomatik olarak rollback olmaktadır.

## İndirim Kuralları Hakkında
- İndirim kuralları hesaba katıralak işlemler gerçekleştirilmiştir.

 
