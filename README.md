# MAX Engage Hub

`MAX Engage Hub`는 AX 교육, AX 진단, AX 맞춤 추천, 일반 문의, 교육 운영, 사례 축적, 블로그 메일링을 하나의 운영 흐름으로 묶는 MVP 프로젝트다.

현재 저장소에는 문서와 함께 `닷홈 무료호스팅 제약`을 고려한 `Laravel Phase 0 + Phase 1` 최소 구현이 포함되어 있다.

## Docs

- [docs/README.md](docs/README.md)
- [docs/07-development-plan.md](docs/07-development-plan.md)
- [docs/08-php-mysql-detailed-architecture.md](docs/08-php-mysql-detailed-architecture.md)
- [docs/09-dothome-free-hosting-profile.md](docs/09-dothome-free-hosting-profile.md)

## Implemented Now

- 퍼블릭 폼 4종
  - 일반 문의
  - 교육 문의
  - AX 진단 요청
  - AX 맞춤 추천 요청
- 이메일 기준 고객 중복 병합
- 고객/활동 이력 저장
- 관리자 로그인
- 관리자 인박스
- 고객 목록/상세
- 운영 메모 및 후속 액션 등록

## Local Setup

1. `cp .env.example .env`
2. MySQL을 쓸 경우 `.env`의 DB 설정을 맞춘다.
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `php artisan serve`

기본 시드 관리자 계정은 `.env.example` 기준으로 아래 값이다.

- email: `admin@example.com`
- password: `ChangeMe1234!`

## Test

- `php artisan test`
