import { asset } from '@/lib/asset';

/** Page-wide background photography layer. */
export function MBackdrop() {
  return (
    <>
      <div className="n" style={{ left: '0px', top: '812px', width: '375px', height: '10311px', background: 'rgba(7, 49, 36, 0.5)' }}>
        <img src={asset('/images/vertical-shot-cabin-forest-surrounded-by-lot-gre-3fe8a54907.webp')} alt="" />
      </div>
    </>
  );
}
