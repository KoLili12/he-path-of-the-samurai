FROM python:3.11-slim

WORKDIR /app

# Install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application
COPY telemetry_generator.py .

# Make script executable
RUN chmod +x telemetry_generator.py

# Create data directory and set up non-root user
RUN mkdir -p /data/csv && \
    useradd -m -u 1000 telemetry && \
    chown -R telemetry:telemetry /app /data/csv
USER telemetry

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD python3 -c "import sys; sys.exit(0)"

# Run generator
CMD ["python3", "-u", "telemetry_generator.py"]
