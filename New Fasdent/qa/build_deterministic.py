from pathlib import Path
import zipfile, sys, os
src=Path(sys.argv[1]).resolve(); out=Path(sys.argv[2]).resolve(); root_name=sys.argv[3]
epoch=(2026,8,14,0,0,0)
files=sorted([p for p in src.rglob('*') if p.is_file()], key=lambda p: p.relative_to(src).as_posix())
with zipfile.ZipFile(out,'w',compression=zipfile.ZIP_DEFLATED,compresslevel=9,strict_timestamps=True) as z:
    for p in files:
        rel=(Path(root_name)/p.relative_to(src)).as_posix()
        zi=zipfile.ZipInfo(rel, date_time=epoch)
        zi.create_system=3
        mode=0o755 if os.access(p, os.X_OK) else 0o644
        zi.external_attr=(mode & 0xFFFF)<<16
        zi.compress_type=zipfile.ZIP_DEFLATED
        zi.flag_bits |= 0x800
        z.writestr(zi,p.read_bytes(),compress_type=zipfile.ZIP_DEFLATED,compresslevel=9)
