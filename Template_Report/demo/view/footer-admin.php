<footer>
    <div class="text-center mt-3">
          <p class="mb-0">Copyright
            &copy; <span id="year"></span> 2025 TeaV. All rights reserved.
          </p>
        </div>
</footer>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const donutChartData = <?php echo json_encode($donut_data); ?>;
    const lineChartData = <?php echo json_encode($monthly_data); ?>;
</script>
<script src="layout/js/jquery-admin.js"></script>
</body>
</html>
