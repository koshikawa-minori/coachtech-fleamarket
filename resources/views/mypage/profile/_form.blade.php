<form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- ユーザー名（必須・users.name を更新） --}}
    <div>
        <label for="name">ユーザー名</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}">
        @error('name') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- プロフィール画像（任意：jpeg/png） --}}
    <div>
        <label for="image_path">プロフィール画像</label>
        <input id="image_path" type="file" name="image_path" accept=".jpeg,.jpg,.png">
        @error('image_path') <p class="error">{{ $message }}</p> @enderror

        @if(isset($profile) && $profile?->image_path)
            <div style="margin-top:8px;">
                <img src="{{ asset('storage/' . $profile->image_path) }}" alt="現在のプロフィール画像" style="max-width:160px;">
            </div>
        @endif
    </div>

    {{-- 郵便番号（任意：ハイフンあり8文字） --}}
    <div>
        <label for="postal_code">郵便番号</label>
        <input id="postal_code" type="text" name="postal_code" placeholder="123-4567"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}">
        @error('postal_code') <p class="error">{{ $message }}</p> @enderror
    </div>

    {{-- 住所（任意：最大255） --}}
    <div>
        <label for="address">住所</label>
        <input id="address" type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
        @error('address') <p class="error">{{ $message }}</p> @enderror
    </div>

    <button type="submit">保存する</button>
</form>
