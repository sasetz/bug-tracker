# Unnamed Bug Tracker

Unnamed But Tracker is a back-end application for tracking the progress on bugs in projects, built using [Laravel 9](https://github.com/laravel/laravel/tree/9.x)

## About this application

This is a personal project that is meant for studying and upgrading my skill set. Right now it is a token-auth API that can be later used by an SPA

## Milestones

- [x] Add authentication
- [x] Implement projects
- [x] Implement tickets
- [x] Implement updates
- [ ] Cover whole project with tests
- [ ] Add SPA
- [ ] Add Laravel Sail
- [ ] Deploy

## REST endpoints
| Method | URL                          |
|--------|------------------------------|
| POST   | /login                       |
| POST   | /register                    |
| GET    | /dashboard                   |
| POST   | /email/verify/{id}/{hash}    |
| GET    | /invites                     |
| GET    | /invites/{invite}            |
| DELETE | /invites/{invite}            |
| PATCH  | /invites/{invite}            |
| PATCH  | /invites/{invite}            |
| GET    | /projects                    |
| POST   | /projects                    |
| DELETE | /projects                    |
| POST   | /projects/invites            |
| GET    | /projects/tickets            |
| POST   | /projects/tickets            |
| GET    | /projects/users              |
| POST   | /projects/users/{user}/admin |
| GET    | /ticket/{ticket}             |
| PATCH  | /ticket/{ticket}             |
| DELETE | /ticket/{ticket}             |
| PATCH  | /ticket/{ticket}/subscribe   |
| PATCH  | /ticket/{ticket}/unsubscribe |
| GET    | /user                        |
| DELETE | /user                        |
| PATCH  | /user                        |
| GET    | /users/{user}                |

## License

This repository is licensed under [MIT License](https://opensource.org/licenses/MIT)