import { asset } from '@/lib/asset';

/** Page-wide background photography layer. */
export function DBackdrop() {
  return (
    <>
      <div className="n" style={{ left: '0px', top: '944px', width: '1920px', height: '10463px', background: 'rgba(7, 49, 36, 0.5)' }}>
        <img src={asset('/images/vertical-shot-cabin-forest-surrounded-by-lot-gre-ccf609efd1.webp')} alt="" />
      </div>
    </>
  );
}
