<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            [
                'title' => 'Anime',
                'description' => 'Anime',
            ],
            [
                'title' => 'Chiến Tranh',
                'description' => 'Chiến Tranh',
            ],
            [
                'title' => 'Chiếu Rạp',
                'description' => 'Chiếu Rạp',
            ],
            [
                'title' => 'Chuyển Thể',
                'description' => 'Chuyển Thể',
            ],
            [
                'title' => 'Chính Kịch',
                'description' => 'Chính Kịch',
            ],
            [
                'title' => 'Chính Luận',
                'description' => 'Chính Luận',
            ],
            [
                'title' => 'Chính Trị',
                'description' => 'Chính Trị',
            ],
            [
                'title' => 'Chương Trình Truyền Hình',
                'description' => 'Chương Trình Truyền Hình',
            ],
            [
                'title' => 'Cung Đấu',
                'description' => 'Cung Đấu',
            ],
            [
                'title' => 'Cuối Tuần',
                'description' => 'Cuối Tuần',
            ],
            [
                'title' => 'Cổ Trang',
                'description' => 'Cổ Trang',
            ],
            [
                'title' => 'Cổ Tích',
                'description' => 'Cổ Tích',
            ],
            [
                'title' => 'Cổ Điển',
                'description' => 'Cổ Điển',
            ],
            [
                'title' => 'DC',
                'description' => 'DC',
            ],
            [
                'title' => 'Gay Cấn',
                'description' => 'Gay Cấn',
            ],
            [
                'title' => 'Gia Đình',
                'description' => 'Gia Đình',
            ],
            [
                'title' => 'Giáng Sinh',
                'description' => 'Giáng Sinh',
            ],
            [
                'title' => 'Giả Tưởng',
                'description' => 'Giả Tưởng',
            ],
            [
                'title' => 'Hoàng Cung',
                'description' => 'Hoàng Cung',
            ],
            [
                'title' => 'Hoạt Hình',
                'description' => 'Hoạt Hình',
            ],
            [
                'title' => 'Hài',
                'description' => 'Hài',
            ],
            [
                'title' => 'Hành Động',
                'description' => 'Hành Động',
            ],
            [
                'title' => 'Hình Sự',
                'description' => 'Hình Sự',
            ],
            [
                'title' => 'Học Đường',
                'description' => 'Học Đường',
            ],
            [
                'title' => 'Khoa Học',
                'description' => 'Khoa Học',
            ],
            [
                'title' => 'Kinh Dị',
                'description' => 'Kinh Dị',
            ],
            [
                'title' => 'Kinh Điển',
                'description' => 'Kinh Điển',
            ],
            [
                'title' => 'Kịch Nói',
                'description' => 'Kịch Nói',
            ],
            [
                'title' => 'Kỳ Ảo',
                'description' => 'Kỳ Ảo',
            ],
            [
                'title' => 'LGBT+',
                'description' => 'LGBT+',
            ],
            [
                'title' => 'Lãng Mạn',
                'description' => 'Lãng Mạn',
            ],
            [
                'title' => 'Lịch Sử',
                'description' => 'Lịch Sử',
            ],
            [
                'title' => 'Marvel',
                'description' => 'Marvel',
            ],
            [
                'title' => 'Miền Viễn Tây',
                'description' => 'Miền Viễn Tây',
            ],
            [
                'title' => 'Nghề Nghiệp',
                'description' => 'Nghề Nghiệp',
            ],
            [
                'title' => 'Nhạc Kịch',
                'description' => 'Nhạc Kịch',
            ],
            [
                'title' => 'Phiêu Lưu',
                'description' => 'Phiêu Lưu',
            ],
            [
                'title' => 'Phép Thuật',
                'description' => 'Phép Thuật',
            ],
            [
                'title' => 'Siêu Anh Hùng',
                'description' => 'Siêu Anh Hùng',
            ],
            [
                'title' => 'Thiếu Nhi',
                'description' => 'Thiếu Nhi',
            ],
            [
                'title' => 'Thần Thoại',
                'description' => 'Thần Thoại',
            ],
            [
                'title' => 'Thể Thao',
                'description' => 'Thể Thao',
            ],
            [
                'title' => 'Truyền Hình Thực Tế',
                'description' => 'Truyền Hình Thực Tế',
            ],
            [
                'title' => 'Tuổi Trẻ',
                'description' => 'Tuổi Trẻ',
            ],
            [
                'title' => 'Tài Liệu',
                'description' => 'Tài Liệu',
            ],
            [
                'title' => 'Tâm Lý',
                'description' => 'Tâm Lý',
            ],
            [
                'title' => 'Tình Cảm',
                'description' => 'Tình Cảm',
            ],
            [
                'title' => 'Tập Luyện',
                'description' => 'Tập Luyện',
            ],
            [
                'title' => 'Viễn Tưởng',
                'description' => 'Viễn Tưởng',
            ],
            [
                'title' => 'Võ Thuật',
                'description' => 'Võ Thuật',
            ],
            [
                'title' => 'Xuyên Không',
                'description' => 'Xuyên Không',
            ],
            [
                'title' => 'Đời Thường',
                'description' => 'Đời Thường',
            ],
        ];
        foreach ($genres as &$genre) {
            $genre['slug'] = Str::slug($genre['title']);
        }

        DB::table('genres')->insert($genres);
    }
}
