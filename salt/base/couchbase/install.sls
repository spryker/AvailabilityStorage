# Install couchbase server

libssl:
  pkg:
    - installed
    - name: libssl0.9.8
    - require:
      - pkgrepo: pav-v2

couchbase-server:
  pkg:
    - installed
    - sources:
      - couchbase-server: http://packages.couchbase.com/releases/2.2.0/couchbase-server-community_2.2.0_x86_64_openssl098.deb
    - require:
      - pkg: libssl
  service:
    - running
    - enable: True
    - require:
      - pkg: couchbase-server

