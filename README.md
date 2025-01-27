# Agahh
Agahh は、アガーというキャラクターとコミュニケーションをとりながら日々を記録していくアプリです！

日々を記録しながら、アガーに意見を聞いたり、たわいもない話ができます。
その他にもアガーと一緒に人生を豊かにできるような機能を拡充予定です！

## 主な機能
- [ ] アガーとテキストチャットできる機能
- [ ] 日記機能
  - [ ] 日記についてアガーがコメントしてくれる
- [ ] TODO機能
- [ ] リマインド機能 
- [ ] アガーと遊べる機能
- [ ] 話し相手を追加できる機能(ペットなど)

## 使用技術
- バックエンド: [Laravel](https://laravel.com/docs/11.x)
- フロント: [Vue](https://vuejs.org/)
- バックエンド/フロント結合: [Inertia.js](https://inertiajs.com/)
- CSSフレームワーク: [Tailwind CSS](https://tailwindcss.com/)
- UIフレームワーク: [shadcn-vue](https://www.shadcn-vue.com/)
- PHPテストフレームワーク: [Pest](https://pestphp.com/)
- DB: [SQLite](https://www.sqlite.org/)
- フロントビルド: [Vite](https://ja.vitejs.dev/)
- CI/CD: [GitHub Actions](https://docs.github.com/ja/actions)

## 環境構築
- [Laravel Herd](https://herd.laravel.com/)をインストール
- `~/Herd`で`git clone `
- 以下コマンドを実行
```bash
cd soccion
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate fresh --seed 
npm install
npm run dev 
```

http://agahh.test へアクセスして表示を確認




