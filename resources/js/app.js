import { start } from './motion';

/* Motor DOM hazır olur olmaz başlar. Betik <head> içinde defer ile
   yüklendiği için DOMContentLoaded'i beklemek yeterli. */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', start, { once: true });
} else {
  start();
}
